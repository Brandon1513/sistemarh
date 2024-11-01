<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodoVacacion extends Model
{
    use HasFactory;
    protected $table = 'periodos_vacaciones'; // Especifica el nombre de la tabla
    protected $fillable = [
        'empleado_id',
        'anio',
        'dias_corresponden',
        'dias_disponibles',
    ];

    // Relación con el modelo User
    public function empleado()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }
}
