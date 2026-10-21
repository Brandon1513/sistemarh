<?php

namespace App\Services;

use App\Mail\NotificacionSolicitudPermiso;
use App\Models\SolicitudPermiso;
use App\Models\User;
use App\Notifications\EstadoPermisoActualizadoNotification;
use App\Services\Concerns\GestionaSolicitudes;
use App\Support\CalendarioLaboral;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PermisosService
{
    use GestionaSolicitudes;

    public const TIPOS = ['Permiso', 'Comisión', 'Suspensión'];
    public const GOCE = ['Con Goce de Sueldo', 'Sin Goce de Sueldo'];

    public function __construct(private AgendaEmpleado $agenda)
    {
    }

    /**
     * Reglas compartidas por el formulario web y la API.
     */
    public static function reglas(): array
    {
        return [
            'fecha_inicio'          => 'required|date',
            'fecha_termino'         => 'required|date|after_or_equal:fecha_inicio',
            'motivo'                => 'required|string|max:500',
            'tipo_permiso'          => ['required', Rule::in(self::GOCE)],
            'tipo'                  => ['required', Rule::in(self::TIPOS)],
            'fecha_regreso_laborar' => 'nullable|date|after_or_equal:fecha_termino',
            'archivo'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public function solicitar(User $empleado, array $datos, ?UploadedFile $archivo = null): SolicitudPermiso
    {
        // El departamento sale del empleado; el del formulario solo se usa si el empleado no tiene uno.
        $departamentoId = $empleado->departamento_id ?? ($datos['departamento_id'] ?? null);

        if (! $departamentoId) {
            $this->error('departamento_id', 'Tu usuario no tiene un departamento asignado. Contacta a Recursos Humanos.');
        }

        $inicio = Carbon::parse($datos['fecha_inicio']);
        $fin = Carbon::parse($datos['fecha_termino']);

        // Un permiso que cae solo en domingos o festivos no justifica nada
        if (CalendarioLaboral::diasHabiles($inicio, $fin) === 0) {
            $this->error('fecha_inicio', 'Las fechas que elegiste son domingo o día festivo. Un permiso debe incluir al menos un día laboral.');
        }

        // No dos permisos que se crucen
        $empalme = SolicitudPermiso::where('empleado_id', $empleado->id)
            ->whereIn('estado', ['pendiente', 'aprobado'])
            ->whereDate('fecha_inicio', '<=', $fin)
            ->whereDate('fecha_termino', '>=', $inicio)
            ->first();

        if ($empalme) {
            $this->error(
                'fecha_inicio',
                'Ya tienes un permiso ' . $empalme->estado . ' del '
                    . Carbon::parse($empalme->fecha_inicio)->format('d/m/Y') . ' al '
                    . Carbon::parse($empalme->fecha_termino)->format('d/m/Y') . ' que se cruza con estas fechas.'
            );
        }

        // Ni un permiso en medio de unas vacaciones
        if ($conflicto = $this->agenda->vacacionesEn($empleado, $inicio, $fin)) {
            $this->error('fecha_inicio', $conflicto);
        }

        $ruta = $archivo?->store('archivos_permisos', 'public');

        try {
            $permiso = SolicitudPermiso::create([
                'empleado_id'           => $empleado->id,
                'departamento_id'       => $departamentoId,
                'fecha_inicio'          => $datos['fecha_inicio'],
                'fecha_termino'         => $datos['fecha_termino'],
                'motivo'                => $datos['motivo'],
                'tipo_permiso'          => $datos['tipo_permiso'],
                'tipo'                  => $datos['tipo'],
                'fecha_regreso_laborar' => $datos['fecha_regreso_laborar'] ?? CalendarioLaboral::siguienteHabil($fin)->toDateString(),
                'archivo'               => $ruta,
                'estado'                => 'pendiente',
            ]);
        } catch (\Throwable $e) {
            // Si no se pudo guardar el registro, no dejamos el archivo huérfano
            if ($ruta) {
                Storage::disk('public')->delete($ruta);
            }
            throw $e;
        }

        $this->sinRomper(function () use ($empleado, $permiso) {
            if ($empleado->supervisor?->email) {
                Mail::to($empleado->supervisor->email)->send(new NotificacionSolicitudPermiso($permiso));
            }
        });

        return $permiso;
    }

    public function aprobar(SolicitudPermiso $permiso): SolicitudPermiso
    {
        return $this->resolver($permiso, 'aprobado');
    }

    public function rechazar(SolicitudPermiso $permiso): SolicitudPermiso
    {
        return $this->resolver($permiso, 'rechazado');
    }

    /**
     * El empleado cancela su propio permiso pendiente (y se borra el archivo adjunto).
     */
    public function cancelar(SolicitudPermiso $permiso): void
    {
        $archivo = null;

        DB::transaction(function () use ($permiso, &$archivo) {
            $actual = $this->bloquearPendiente($permiso);
            $archivo = $actual->archivo;
            $actual->delete();
        });

        if ($archivo) {
            $this->sinRomper(fn () => Storage::disk('public')->delete($archivo));
        }
    }

    private function resolver(SolicitudPermiso $permiso, string $estado): SolicitudPermiso
    {
        DB::transaction(function () use ($permiso, $estado) {
            $actual = $this->bloquearPendiente($permiso);
            $actual->update(['estado' => $estado]);
        });

        $permiso->refresh();

        $this->sinRomper(fn () => $permiso->empleado?->notify(new EstadoPermisoActualizadoNotification($estado)));

        return $permiso;
    }
}