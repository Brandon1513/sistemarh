<?php

namespace App\Exports;

use App\Exports\Concerns\BrandedExport;

/**
 * Libro mayor de solicitudes de vacaciones (solicitudes_vacaciones).
 */
class VacacionesExport extends BrandedExport
{
    protected function reportTitle(): string
    {
        return 'Solicitudes de Vacaciones';
    }

    protected function columns(): array
    {
        return [
            'Folio'              => ['width' => 13, 'bold' => true, 'value' => fn ($v) => self::folio('VAC', $v->id)],
            'No. empleado'       => ['width' => 12, 'value' => fn ($v) => $v->empleado?->clave_empleado],
            'Empleado'           => ['width' => 30, 'value' => fn ($v) => $v->empleado?->name],
            'Puesto'             => ['width' => 22, 'value' => fn ($v) => $v->empleado?->puesto_empleado],
            'Departamento'       => ['width' => 20, 'value' => fn ($v) => $v->departamento?->name],
            'Jefe inmediato'     => ['width' => 26, 'value' => fn ($v) => $v->empleado?->supervisor?->name],
            'Periodo'            => ['width' => 11, 'value' => fn ($v) => $v->periodo_correspondiente],
            'Fecha solicitud'    => ['width' => 13, 'type' => 'date', 'value' => fn ($v) => $v->fecha_solicitud ?? $v->created_at],
            'Días corresponden'  => ['width' => 12, 'type' => 'number', 'value' => fn ($v) => $v->dias_corresponden],
            'Días solicitados'   => ['width' => 12, 'type' => 'number', 'sum' => true, 'value' => fn ($v) => $v->dias_solicitados],
            'Días pendientes'    => ['width' => 12, 'type' => 'number', 'value' => fn ($v) => $v->dias_pendientes],
            'Inicio'             => ['width' => 12, 'type' => 'date', 'value' => fn ($v) => $v->fecha_inicio],
            'Fin'                => ['width' => 12, 'type' => 'date', 'value' => fn ($v) => $v->fecha_fin],
            'Reincorporación'    => ['width' => 14, 'type' => 'date', 'value' => fn ($v) => $v->fecha_reincorporacion],
            'Estado'             => ['width' => 12, 'type' => 'status', 'value' => fn ($v) => $v->estado],
        ];
    }
}
