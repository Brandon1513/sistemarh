<?php

namespace App\Services;

use App\Models\SolicitudPermiso;
use App\Models\SolicitudVacacion;
use App\Models\User;
use Carbon\Carbon;

/**
 * Revisa si un empleado ya tiene una ausencia (vacaciones o permiso) en un rango de fechas.
 * Solo cuentan las solicitudes pendientes o aprobadas.
 */
class AgendaEmpleado
{
    public function vacacionesEn(User $empleado, Carbon $inicio, Carbon $fin): ?string
    {
        $vacacion = SolicitudVacacion::where('empleado_id', $empleado->id)
            ->whereIn('estado', ['pendiente', 'aprobado'])
            ->whereDate('fecha_inicio', '<=', $fin)
            ->whereDate('fecha_fin', '>=', $inicio)
            ->first();

        return $vacacion
            ? "En esas fechas ya hay una solicitud de vacaciones {$vacacion->estado} del {$this->rango($vacacion->fecha_inicio, $vacacion->fecha_fin)}."
            : null;
    }

    public function permisoEn(User $empleado, Carbon $inicio, Carbon $fin): ?string
    {
        $permiso = SolicitudPermiso::where('empleado_id', $empleado->id)
            ->whereIn('estado', ['pendiente', 'aprobado'])
            ->whereDate('fecha_inicio', '<=', $fin)
            ->whereDate('fecha_termino', '>=', $inicio)
            ->first();

        return $permiso
            ? "En esas fechas ya hay un permiso de ausencia {$permiso->estado} del {$this->rango($permiso->fecha_inicio, $permiso->fecha_termino)}."
            : null;
    }

    private function rango($inicio, $fin): string
    {
        return Carbon::parse($inicio)->format('d/m/Y').' al '.Carbon::parse($fin)->format('d/m/Y');
    }
}
