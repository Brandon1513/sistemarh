<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudVacacion extends Model
{
    use HasFactory;
    protected $table = 'solicitudes_vacaciones'; // Especifica el nombre correcto de la tabla
    protected $fillable = [
        'empleado_id',
        'departamento_id',
        'fecha_solicitud',
        'dias_corresponden',
        'dias_solicitados',
        'dias_pendientes',
        'periodo_correspondiente',
        'fecha_inicio',
        'fecha_fin',
        'fecha_reincorporacion',
        'estado',
    ];

    // Relación con el modelo User
    public function empleado()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }

    // Relación con el modelo Departamento
    public function departamento()
    {
        return $this->belongsTo(Department::class, 'departamento_id');
    }
}
