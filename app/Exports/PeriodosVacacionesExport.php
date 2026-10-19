<?php

namespace App\Exports;

use App\Exports\Concerns\BrandedExport;
use App\Models\PeriodoVacacion;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Saldos de vacaciones por empleado y periodo (periodos_vacaciones).
 */
class PeriodosVacacionesExport extends BrandedExport
{
    protected function reportTitle(): string
    {
        return 'Periodos de Vacaciones';
    }

    protected function records(): Collection
    {
        // Incluye empleados dados de baja (User oculta a los inactivos con un scope global)
        return PeriodoVacacion::with([
                'empleado' => fn ($q) => $q->withoutGlobalScope('activo'),
                'empleado.departamento',
            ])
            ->get()
            ->sortBy([
                fn ($a, $b) => ($b->empleado?->activo ?? false) <=> ($a->empleado?->activo ?? false),
                fn ($a, $b) => strcasecmp(Str::ascii((string) $a->empleado?->name), Str::ascii((string) $b->empleado?->name)),
                fn ($a, $b) => $b->anio <=> $a->anio,
            ])
            ->values();
    }

    protected function employeeName($periodo): string
    {
        $employee = $periodo->empleado;

        if (! $employee) {
            return 'Empleado no encontrado';
        }

        return $employee->activo ? $employee->name : "{$employee->name} (baja)";
    }

    protected function columns(): array
    {
        return [
            'No. empleado'       => ['width' => 13, 'value' => fn ($p) => $p->empleado?->clave_empleado],
            'Empleado'           => ['width' => 30, 'bold' => true, 'value' => fn ($p) => $this->employeeName($p)],
            'Puesto'             => ['width' => 22, 'value' => fn ($p) => $p->empleado?->puesto_empleado],
            'Departamento'       => ['width' => 20, 'value' => fn ($p) => $p->empleado?->departamento?->name],
            'Fecha de ingreso'   => ['width' => 13, 'type' => 'date', 'value' => fn ($p) => $p->empleado?->fecha_ingreso],
            'Antigüedad (años)'  => ['width' => 12, 'type' => 'number', 'value' => fn ($p) => $p->empleado?->fecha_ingreso
                ? (int) Carbon::parse($p->empleado->fecha_ingreso)->diffInYears(now())
                : null],
            'Periodo'            => ['width' => 10, 'value' => fn ($p) => $p->anio],
            'Días corresponden'  => ['width' => 12, 'type' => 'number', 'sum' => true, 'value' => fn ($p) => $p->dias_corresponden],
            'Días disfrutados'   => ['width' => 12, 'type' => 'number', 'sum' => true, 'value' => fn ($p) => $p->dias_corresponden !== null && $p->dias_disponibles !== null
                ? $p->dias_corresponden - $p->dias_disponibles
                : null],
            'Días disponibles'   => ['width' => 12, 'type' => 'number', 'sum' => true, 'value' => fn ($p) => $p->dias_disponibles],
            'Estado'             => ['width' => 11, 'type' => 'status', 'value' => fn ($p) => $p->activo ? 'Activo' : 'Inactivo'],
            'Registrado el'      => ['width' => 13, 'type' => 'date', 'value' => fn ($p) => $p->created_at],
        ];
    }
}
