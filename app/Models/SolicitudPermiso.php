<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudPermiso extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_permisos'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $fillable = [
        'empleado_id',
        'departamento_id',
        'fecha_inicio',
        'fecha_termino',
        'motivo',
        'tipo_permiso',
        'estado',
        'fecha_regreso_laborar',
        'tipo',
        'dia_descanso',
        
    ];

    // Relación con el empleado (usuario)
    public function empleado()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }

    // Relación con el departamento
    public function departamento()
    {
        return $this->belongsTo(Department::class, 'departamento_id');
    }

    // Relación con el supervisor
   // public function supervisor()
   // {
   //     return $this->belongsTo(User::class, 'supervisor_id');
   // }
}
