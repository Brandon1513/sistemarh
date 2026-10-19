<?php

namespace App\Exports;

use App\Exports\Concerns\BrandedExport;

/**
 * Control de ausencias: permisos, comisiones y suspensiones (solicitudes_permisos).
 */
class PermisosExport extends BrandedExport
{
    protected function reportTitle(): string
    {
        return 'Control de Ausencias del Personal';
    }

    protected function sheetTitle(): string
    {
        return 'Control de Ausencias';
    }

    protected function columns(): array
    {
        return [
            'Folio'              => ['width' => 13, 'bold' => true, 'value' => fn ($p) => self::folio('AU', $p->id)],
            'No. empleado'       => ['width' => 12, 'value' => fn ($p) => $p->empleado?->clave_empleado],
            'Empleado'           => ['width' => 30, 'value' => fn ($p) => $p->empleado?->name],
            'Puesto'             => ['width' => 22, 'value' => fn ($p) => $p->empleado?->puesto_empleado],
            'Departamento'       => ['width' => 20, 'value' => fn ($p) => $p->departamento?->name],
            'Jefe inmediato'     => ['width' => 26, 'value' => fn ($p) => $p->empleado?->supervisor?->name],
            'Tipo'               => ['width' => 12, 'value' => fn ($p) => $p->tipo],
            'Goce de sueldo'     => ['width' => 13, 'value' => fn ($p) => match ($p->tipo_permiso) {
                'Con Goce de Sueldo' => 'Con goce',
                'Sin Goce de Sueldo' => 'Sin goce',
                default              => $p->tipo_permiso,
            }],
            'Fecha inicio'       => ['width' => 12, 'type' => 'date', 'value' => fn ($p) => $p->fecha_inicio],
            'Fecha término'      => ['width' => 12, 'type' => 'date', 'value' => fn ($p) => $p->fecha_termino],
            'Días'               => ['width' => 8,  'type' => 'number', 'sum' => true, 'value' => fn ($p) => self::inclusiveDays($p->fecha_inicio, $p->fecha_termino)],
            'Regresa a laborar'  => ['width' => 13, 'type' => 'date', 'value' => fn ($p) => $p->fecha_regreso_laborar],
            'Día de descanso'    => ['width' => 13, 'value' => fn ($p) => $p->dia_descanso],
            'Motivo'             => ['width' => 40, 'type' => 'long', 'value' => fn ($p) => $p->motivo],
            'Documento'          => ['width' => 11, 'value' => fn ($p) => $p->archivo ? 'Sí' : 'No'],
            'Estado'             => ['width' => 12, 'type' => 'status', 'value' => fn ($p) => $p->estado],
            'Solicitado el'      => ['width' => 13, 'type' => 'date', 'value' => fn ($p) => $p->created_at],
        ];
    }
}
