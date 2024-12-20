<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VacacionesExport implements FromCollection, WithHeadings
{
    protected $solicitudes;

    public function __construct($solicitudes)
    {
        $this->solicitudes = $solicitudes;
    }

    public function collection()
    {
        return $this->solicitudes->map(function ($solicitud) {
            return [
                'Empleado' => $solicitud->empleado->name,
                'Departamento' => $solicitud->departamento->name ?? 'Sin Departamento',
                'Fecha Inicio' => $solicitud->fecha_inicio,
                'Fecha Fin' => $solicitud->fecha_fin,
                'Días Solicitados' => $solicitud->dias_solicitados,
                'Estado' => ucfirst($solicitud->estado),
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
