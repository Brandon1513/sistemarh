<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PermissionsExport implements FromCollection, WithHeadings
{
    protected $permissions;

    public function __construct($permissions)
    {
        $this->permissions = $permissions;
    }

    public function collection()
    {
        return $this->permissions->map(function ($permission) {
            return [
                'Empleado' => $permission->user->name,
                'Jefe' => $permission->user->supervisor ? $permission->user->supervisor->name : 'Sin Jefe',
                'Departamento' => $permission->department->name,
                'Horario Oficial' => $permission->official_schedule,
                'Hora de Entrada/Salida' => $permission->entry_exit_time,
                'Fecha del Permiso' => $permission->date,
                'Motivo' => $permission->reason,
                'Estado' => ucfirst($permission->status),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Empleado',
            'Jefe',
            'Departamento',
            'Horario Oficial',
            'Hora de Entrada/Salida',
            'Fecha del Permiso',
            'Motivo',
            'Estado',
        ];
    }
}
