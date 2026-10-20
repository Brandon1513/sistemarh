<?php

namespace App\Services;

use App\Mail\VacationRequestNotification;
use App\Models\PeriodoVacacion;
use App\Models\SolicitudVacacion;
use App\Models\User;
use App\Notifications\VacationApprovedNotification;
use App\Notifications\VacationRejectedNotification;
use App\Notifications\VacationRHNotification;
use App\Notifications\VacationSecurityNotification;
use App\Services\Concerns\GestionaSolicitudes;
use App\Support\CalendarioLaboral;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class VacacionesService
{
    use GestionaSolicitudes;

    public function __construct(private AgendaEmpleado $agenda)
    {
    }

    /**
     * Crea una solicitud de vacaciones y descuenta los días del periodo.
     *
     * $diasEsperados es opcional: si viene (como en el formulario web), debe coincidir
     * con los días hábiles del rango; si no viene (como en la app), se calculan.
     */
    public function solicitar(User $empleado, int $anioPeriodo, string $inicio, string $fin, ?int $diasEsperados = null): SolicitudVacacion
    {
        $fechaInicio = Carbon::parse($inicio)->startOfDay();
        $fechaFin = Carbon::parse($fin)->startOfDay();

        if ($fechaInicio->lt(today())) {
            $this->error('fecha_inicio_vacaciones', 'La fecha de inicio no puede ser anterior a hoy.');
        }

        if ($fechaFin->lt($fechaInicio)) {
            $this->error('fecha_termino_vacaciones', 'La fecha de término no puede ser anterior a la de inicio.');
        }

        if (! CalendarioLaboral::esHabil($fechaInicio)) {
            $this->error('fecha_inicio_vacaciones', 'La fecha de inicio cae en domingo o día festivo.');
        }

        if (! CalendarioLaboral::esHabil($fechaFin)) {
            $this->error('fecha_termino_vacaciones', 'La fecha de término cae en domingo o día festivo.');
        }

        $dias = CalendarioLaboral::diasHabiles($fechaInicio, $fechaFin);

        if ($diasEsperados !== null && $dias !== $diasEsperados) {
            $this->error(
                'fecha_termino_vacaciones',
                "Del {$fechaInicio->format('d/m/Y')} al {$fechaFin->format('d/m/Y')} hay {$dias} días hábiles, pero solicitaste {$diasEsperados}."
            );
        }

        $this->validarSinEmpalme($empleado, $fechaInicio, $fechaFin);
        $this->validarSinPermisos($empleado, $fechaInicio, $fechaFin);

        $solicitud = DB::transaction(function () use ($empleado, $anioPeriodo, $fechaInicio, $fechaFin, $dias) {
            $periodo = $this->bloquearPeriodo($empleado, $anioPeriodo);

            if (! $periodo) {
                $this->error('periodo_correspondiente', 'El periodo seleccionado no existe o no está activo.');
            }

            if ($periodo->dias_disponibles < $dias) {
                $this->error(
                    'dias_solicitados',
                    "Pides {$dias} días y en el periodo {$anioPeriodo} solo tienes {$periodo->dias_disponibles}."
                );
            }

            $periodo->decrement('dias_disponibles', $dias);

            return SolicitudVacacion::create([
                'empleado_id'             => $empleado->id,
                'departamento_id'         => $empleado->departamento_id,
                'fecha_solicitud'         => now(),
                'dias_corresponden'       => $periodo->dias_corresponden,
                'dias_solicitados'        => $dias,
                'dias_pendientes'         => $periodo->dias_disponibles,
                'periodo_correspondiente' => $periodo->anio,
                'fecha_inicio'            => $fechaInicio->toDateString(),
                'fecha_fin'               => $fechaFin->toDateString(),
                'fecha_reincorporacion'   => CalendarioLaboral::siguienteHabil($fechaFin)->toDateString(),
                'estado'                  => 'pendiente',
            ]);
        });

        $this->notificarJefe($empleado, $solicitud);

        return $solicitud;
    }

    /**
     * RH registra una vacación espontánea a nombre de un empleado.
     * RH define los días y la fecha de regreso; se permiten fechas pasadas.
     */
    public function registrarEspontanea(
        User $empleado,
        User $registradoPor,
        int $anioPeriodo,
        string $inicio,
        string $fin,
        int $dias,
        string $fechaPresentarse,
    ): SolicitudVacacion {
        $fechaInicio = Carbon::parse($inicio)->startOfDay();
        $fechaFin = Carbon::parse($fin)->startOfDay();

        if ($fechaFin->lt($fechaInicio)) {
            $this->error('fecha_termino_vacaciones', 'La fecha de término no puede ser anterior a la de inicio.');
        }

        $this->validarSinEmpalme($empleado, $fechaInicio, $fechaFin);
        $this->validarSinPermisos($empleado, $fechaInicio, $fechaFin);

        $solicitud = DB::transaction(function () use ($empleado, $registradoPor, $anioPeriodo, $fechaInicio, $fechaFin, $dias, $fechaPresentarse) {
            $periodo = $this->bloquearPeriodo($empleado, $anioPeriodo);

            if (! $periodo || $periodo->dias_disponibles < $dias) {
                $this->error('dias_solicitados', 'El empleado no tiene suficientes días disponibles en este período.');
            }

            $periodo->decrement('dias_disponibles', $dias);

            return SolicitudVacacion::create([
                'empleado_id'             => $empleado->id,
                'departamento_id'         => $empleado->departamento_id,
                'fecha_solicitud'         => now(),
                'dias_corresponden'       => $periodo->dias_corresponden,
                'dias_solicitados'        => $dias,
                'dias_pendientes'         => $periodo->dias_disponibles,
                'periodo_correspondiente' => $periodo->anio,
                'fecha_inicio'            => $fechaInicio->toDateString(),
                'fecha_fin'               => $fechaFin->toDateString(),
                'fecha_reincorporacion'   => Carbon::parse($fechaPresentarse)->toDateString(),
                'estado'                  => 'pendiente',
                'tipo'                    => 'espontanea',
                'creado_por'              => $registradoPor->id,
            ]);
        });

        $this->notificarJefe($empleado, $solicitud);

        return $solicitud;
    }

    /**
     * Aprueba una solicitud pendiente. La autorización se revisa antes, con la Policy.
     */
    public function aprobar(SolicitudVacacion $solicitud): SolicitudVacacion
    {
        DB::transaction(function () use ($solicitud) {
            $actual = $this->bloquearPendiente($solicitud);
            $actual->update(['estado' => 'aprobado']);
        });

        $solicitud->refresh();

        $this->sinRomper(fn () => $solicitud->empleado?->notify(new VacationApprovedNotification($solicitud)));
        $this->sinRomper(fn () => $this->notificarRH($solicitud, 'aprobada'));
        $this->sinRomper(function () use ($solicitud) {
            User::whereHas('roles', fn ($q) => $q->where('name', 'Seguridad'))
                ->get()
                ->each(fn (User $persona) => $persona->notify(new VacationSecurityNotification($solicitud)));
        });

        return $solicitud;
    }

    /**
     * Rechaza una solicitud pendiente y devuelve los días a su periodo.
     */
    public function rechazar(SolicitudVacacion $solicitud): SolicitudVacacion
    {
        DB::transaction(function () use ($solicitud) {
            $actual = $this->bloquearPendiente($solicitud);
            $this->devolverDias($actual);
            $actual->update(['estado' => 'rechazado']);
        });

        $solicitud->refresh();

        $this->sinRomper(fn () => $solicitud->empleado?->notify(new VacationRejectedNotification($solicitud)));
        $this->sinRomper(fn () => $this->notificarRH($solicitud, 'rechazada'));

        return $solicitud;
    }

    /**
     * El empleado cancela su propia solicitud pendiente.
     */
    public function cancelar(SolicitudVacacion $solicitud): void
    {
        DB::transaction(function () use ($solicitud) {
            $actual = $this->bloquearPendiente($solicitud);
            $this->devolverDias($actual);
            $actual->delete();
        });
    }

    // ─── Internos ─────────────────────────────────────────────────────────────

    private function bloquearPeriodo(User $empleado, int $anio): ?PeriodoVacacion
    {
        return PeriodoVacacion::where('empleado_id', $empleado->id)
            ->where('anio', $anio)
            ->where('activo', 1)
            ->lockForUpdate()
            ->first();
    }

    private function validarSinEmpalme(User $empleado, Carbon $inicio, Carbon $fin): void
    {
        $traslape = SolicitudVacacion::where('empleado_id', $empleado->id)
            ->whereIn('estado', ['pendiente', 'aprobado'])
            ->whereDate('fecha_inicio', '<=', $fin)
            ->whereDate('fecha_fin', '>=', $inicio)
            ->first();

        if ($traslape) {
            $this->error(
                'fecha_inicio_vacaciones',
                'Ya tienes una solicitud ' . $traslape->estado . ' del '
                    . Carbon::parse($traslape->fecha_inicio)->format('d/m/Y') . ' al '
                    . Carbon::parse($traslape->fecha_fin)->format('d/m/Y') . ' que se cruza con estas fechas.'
            );
        }
    }

    /**
     * Unas vacaciones no pueden caer sobre un permiso de ausencia pendiente o aprobado.
     */
    private function validarSinPermisos(User $empleado, Carbon $inicio, Carbon $fin): void
    {
        if ($conflicto = $this->agenda->permisoEn($empleado, $inicio, $fin)) {
            $this->error('fecha_inicio_vacaciones', $conflicto);
        }
    }

    private function devolverDias(SolicitudVacacion $solicitud): void
    {
        PeriodoVacacion::where('empleado_id', $solicitud->empleado_id)
            ->where('anio', $solicitud->periodo_correspondiente)
            ->lockForUpdate()
            ->first()
            ?->increment('dias_disponibles', $solicitud->dias_solicitados);
    }

    private function notificarJefe(User $empleado, SolicitudVacacion $solicitud): void
    {
        $this->sinRomper(function () use ($empleado, $solicitud) {
            if ($empleado->supervisor?->email) {
                Mail::to($empleado->supervisor->email)->send(new VacationRequestNotification($empleado, $solicitud));
            }
        });
    }

    private function notificarRH(SolicitudVacacion $solicitud, string $resultado): void
    {
        $correos = User::role('recursos_humanos')->pluck('email')->filter()->all();

        if ($correos) {
            Notification::route('mail', $correos)->notify(new VacationRHNotification($solicitud, $resultado));
        }
    }
}