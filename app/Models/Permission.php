<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'employee_permissions'; // Asegúrate de apuntar a la nueva tabla

    protected $fillable = [
        'user_id', 
        'position', 
        'department_id', 
        'official_schedule', 
        'entry_exit_time', 
        'entry_exit_type', // Este es el nuevo campo
        'date', 
        'reason',
        'supporting_document',
        'approved_by', 
        'hr_id', 
        'status'
    ];

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el aprobador (supervisor)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relación con el departamento
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Relación con el HR (Recursos Humanos)
    public function hr()
    {
        return $this->belongsTo(User::class, 'hr_id');
    }
}

