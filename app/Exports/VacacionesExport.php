<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VacacionesExport implements FromCollection, WithHeadings
{
    protected $vacaciones;

    public function __construct($vacaciones)
    {
        $this->vacaciones = $vacaciones;
    }

    public function collection()
    {
        return $this->vacaciones->map(function ($vacacion) {
            return [
                'Empleado' => $vacacion->empleado->name,
                'Departamento' => $vacacion->departamento->name,
                'Fecha Inicio' => $vacacion->fecha_inicio,
                'Fecha Fin' => $vacacion->fecha_fin,
                'Días Solicitados' => $vacacion->dias_solicitados,
                'Estado' => $vacacion->estado,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Empleado',
            'Departamento',
            'Fecha Inicio',
            'Fecha Fin',
            'Días Solicitados',
            'Estado',
        ];
    }
}
