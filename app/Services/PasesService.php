<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use App\Notifications\NotificacionSeguridadPermiso;
use App\Notifications\PaseSalidaActualizadoNotification;
use App\Notifications\PermissionRequested;
use App\Services\Concerns\GestionaSolicitudes;
use App\Support\CalendarioLaboral;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class PasesService
{
    use GestionaSolicitudes;

    public const TIPOS = ['entrada', 'salida'];

    public function __construct(
        private AgendaEmpleado $agenda,
        private PushService $push,
    ) {}

    /**
     * Reglas compartidas por el formulario web y la API.
     */
    public static function reglas(): array
    {
        return [
            'entry_exit_type' => 'required|in:entrada,salida',
            'official_schedule' => 'required',
            'entry_exit_time' => 'required',
            'date' => 'required|date',
            'reason' => 'required|string|max:500',
            'supporting_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'aviso_privacidad' => 'accepted',
        ];
    }

    public function solicitar(User $empleado, array $datos, ?UploadedFile $documento = null): Permission
    {
        // Puesto y departamento salen del empleado; los del formulario solo se usan si faltan.
        $departamentoId = $empleado->departamento_id ?? ($datos['department_id'] ?? null);
        $puesto = $empleado->puesto_empleado ?: ($datos['position'] ?? null);

        if (! $departamentoId) {
            $this->error('department_id', 'Tu usuario no tiene un departamento asignado. Contacta a Recursos Humanos.');
        }

        if (! $puesto) {
            $this->error('position', 'Tu usuario no tiene un puesto asignado. Contacta a Recursos Humanos.');
        }

        $dia = Carbon::parse($datos['date']);

        // No se necesita pase en un día que no se trabaja
        if (! CalendarioLaboral::esHabil($dia, $empleado->sede_id)) {
            $this->error('date', 'Ese día es domingo o festivo, así que no necesitas pase.');
        }

        // No dos pases del mismo tipo el mismo día
        $duplicado = Permission::where('user_id', $empleado->id)
            ->whereIn('status', ['pendiente', 'aprobado'])
            ->whereDate('date', $dia)
            ->where('entry_exit_type', $datos['entry_exit_type'])
            ->exists();

        if ($duplicado) {
            $this->error('date', "Ya tienes un pase de {$datos['entry_exit_type']} pendiente o aprobado para ese día.");
        }

        // Un pase no tiene sentido en un día de vacaciones o de permiso de ausencia
        if ($conflicto = $this->agenda->vacacionesEn($empleado, $dia, $dia) ?? $this->agenda->permisoEn($empleado, $dia, $dia)) {
            $this->error('date', $conflicto);
        }

        $ruta = $documento?->store('documents', 'public');

        try {
            $pase = Permission::create([
                'user_id' => $empleado->id,
                'position' => $puesto,
                'department_id' => $departamentoId,
                'entry_exit_type' => $datos['entry_exit_type'],
                'official_schedule' => $datos['official_schedule'],
                'entry_exit_time' => $datos['entry_exit_time'],
                'date' => $datos['date'],
                'reason' => $datos['reason'],
                'supporting_document' => $ruta,
                'status' => 'pendiente',
            ]);
        } catch (\Throwable $e) {
            if ($ruta) {
                Storage::disk('public')->delete($ruta);
            }
            throw $e;
        }

        $this->sinRomper(function () use ($empleado, $pase) {
            if ($empleado->supervisor) {
                Notification::send($empleado->supervisor, new PermissionRequested($pase));
            }
        });
        $this->sinRomper(function () use ($empleado, $pase) {
            if ($empleado->supervisor) {
                $this->push->enviar(
                    $empleado->supervisor,
                    "Pase de {$pase->entry_exit_type}",
                    PushService::nombreCorto($empleado).', '.Carbon::parse($pase->date)->format('d/m')
                        .' a las '.substr((string) $pase->entry_exit_time, 0, 5),
                    ['destino' => 'aprobaciones', 'tipo' => 'pase', 'id' => $pase->id],
                );
            }
        });

        return $pase;
    }

    public function aprobar(Permission $pase, User $aprobador): Permission
    {
        $this->resolver($pase, $aprobador, 'aprobado');

        $empleado = $pase->user;

        // Seguridad recibe el aviso para dejar pasar al empleado en caseta
        $this->sinRomper(function () use ($pase, $empleado) {
            if (! $empleado) {
                return;
            }

            $data = [
                'nombre_empleado' => $empleado->name,
                'puesto' => $empleado->puesto_empleado ?? 'No especificado',
                'departamento' => $empleado->departamento?->name ?? $pase->department?->name ?? 'No especificado',
                'official_schedule' => $pase->official_schedule,
                'entry_exit_time' => $pase->entry_exit_time,
                'date' => $pase->date,
                'reason' => $pase->reason,
                'supporting_document' => $pase->supporting_document,
                'status' => 'aprobado',
                'entry_exit_type' => $pase->entry_exit_type,
            ];

            User::whereHas('roles', fn ($q) => $q->where('name', 'Seguridad'))
                ->get()
                ->each(fn (User $guardia) => $guardia->notify(new NotificacionSeguridadPermiso($data)));
        });

        return $pase;
    }

    public function rechazar(Permission $pase, User $aprobador): Permission
    {
        return $this->resolver($pase, $aprobador, 'rechazado');
    }

    /**
     * El empleado cancela su propio pase pendiente (y se borra el documento adjunto).
     */
    public function cancelar(Permission $pase): void
    {
        $documento = null;

        DB::transaction(function () use ($pase, &$documento) {
            $actual = $this->bloquearPendiente($pase, 'status');
            $documento = $actual->supporting_document;
            $actual->delete();
        });

        if ($documento) {
            $this->sinRomper(fn () => Storage::disk('public')->delete($documento));
        }
    }

    private function resolver(Permission $pase, User $aprobador, string $estado): Permission
    {
        DB::transaction(function () use ($pase, $aprobador, $estado) {
            $actual = $this->bloquearPendiente($pase, 'status');
            $actual->update(['status' => $estado, 'approved_by' => $aprobador->id]);
        });

        $pase->refresh();

        $this->sinRomper(fn () => $pase->user?->notify(new PaseSalidaActualizadoNotification($estado)));

        $this->sinRomper(fn () => $pase->user && $this->push->enviar(
            $pase->user,
            $estado === 'aprobado' ? "Tu pase de {$pase->entry_exit_type} fue aprobado ✅" : "Tu pase de {$pase->entry_exit_type} fue rechazado",
            Carbon::parse($pase->date)->format('d/m').' a las '.substr((string) $pase->entry_exit_time, 0, 5),
            ['destino' => 'solicitudes'],
        ));

        return $pase;
    }
}
