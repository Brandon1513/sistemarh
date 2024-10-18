<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PermisosExport implements FromCollection, WithHeadings
{
    protected $permisos;

    public function __construct($permisos)
    {
        $this->permisos = $permisos;
    }

    public function collection()
    {
        // Estructura los datos para el Excel
        return $this->permisos->map(function ($permiso) {
            return [
                'Empleado' => $permiso->empleado->nombre,
                'Departamento' => $permiso->departamento->nombre,
                'Fecha Inicio' => $permiso->fecha_inicio,
                'Fecha Término' => $permiso->fecha_termino,
                'Motivo' => $permiso->motivo,
                'Estado' => ucfirst($permiso->estado),
            ];
        });
    }

    public function headings(): array
    {
        // Encabezados del archivo Excel
        return [
            'Empleado', 'Departamento', 'Fecha Inicio', 'Fecha Término', 'Motivo', 'Estado',
        ];
    }
}
