<?php

namespace App\Exports;

use App\Models\PeriodoVacacion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PeriodosVacacionesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return PeriodoVacacion::with('empleado')->get()->map(function ($periodo) {
            return [
                'Empleado' => $periodo->empleado->name ?? 'N/A',
                'Año del Periodo' => $periodo->anio,
                'Días Correspondientes' => $periodo->dias_corresponden,
                'Días Disponibles' => $periodo->dias_disponibles,
                'Estado' => $periodo->activo ? 'Activo' : 'Inactivo',
                'Fecha de Creación' => $periodo->created_at ? $periodo->created_at->format('Y-m-d') : 'No disponible',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Empleado',
            'Año del Periodo',
            'Días Correspondientes',
            'Días Disponibles',
            'Estado',
            'Fecha de Creación',
        ];
    }
}
