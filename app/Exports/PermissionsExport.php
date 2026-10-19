<?php

namespace App\Exports;

use App\Exports\Concerns\BrandedExport;

/**
 * Pases de entrada / salida (employee_permissions).
 */
class PermissionsExport extends BrandedExport
{
    protected function reportTitle(): string
    {
        return 'Pases de Entrada / Salida';
    }

    protected function columns(): array
    {
        return [
            'Folio'              => ['width' => 13, 'bold' => true, 'value' => fn ($p) => self::folio('PS', $p->id)],
            'Fecha'              => ['width' => 12, 'type' => 'date', 'value' => fn ($p) => $p->date],
            'Tipo'               => ['width' => 10, 'value' => fn ($p) => $p->entry_exit_type ? ucfirst($p->entry_exit_type) : null],
            'No. empleado'       => ['width' => 12, 'value' => fn ($p) => $p->user?->clave_empleado],
            'Empleado'           => ['width' => 30, 'value' => fn ($p) => $p->user?->name],
            'Puesto'             => ['width' => 22, 'value' => fn ($p) => $p->position ?: $p->user?->puesto_empleado],
            'Departamento'       => ['width' => 20, 'value' => fn ($p) => $p->department?->name],
            'Jefe inmediato'     => ['width' => 26, 'value' => fn ($p) => $p->user?->supervisor?->name],
            'Horario oficial'    => ['width' => 12, 'type' => 'time', 'value' => fn ($p) => $p->official_schedule],
            'Hora entrada/salida'=> ['width' => 13, 'type' => 'time', 'value' => fn ($p) => $p->entry_exit_time],
            'Motivo'             => ['width' => 40, 'type' => 'long', 'value' => fn ($p) => $p->reason],
            'Documento'          => ['width' => 11, 'value' => fn ($p) => $p->supporting_document ? 'Sí' : 'No'],
            'Estado'             => ['width' => 12, 'type' => 'status', 'value' => fn ($p) => $p->status],
            'Autorizó'           => ['width' => 26, 'value' => fn ($p) => $p->approver?->name],
            'Solicitado el'      => ['width' => 13, 'type' => 'date', 'value' => fn ($p) => $p->created_at],
        ];
    }
}
