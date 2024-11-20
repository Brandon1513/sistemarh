<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // Incluye el trait HasRoles de Spatie

    protected $fillable = [
        'name',
        'email',
        'password',
        'activo',
        'supervisor_id',
        'clave_empleado',   // Agrega este campo
        'fecha_ingreso',    // Agrega este campo
        'puesto_empleado',  // Agrega este campo
        'departamento_id',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected static function boot()
    {
        parent::boot();

        // Agregar Global Scope para usuarios activos
        static::addGlobalScope('activo', function (Builder $builder) {
            $builder->where('activo', true);
        });
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'fecha_ingreso' => 'date',
    ];

    // Conversión de fechas a objetos Carbon
    protected $dates = [
        'fecha_ingreso',   // Agrega este campo si quieres tratarlo como fecha
    ];

    // Relación con el supervisor del usuario
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // Relación con los subordinados del usuario
    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    // Relación con los permisos que ha solicitado el usuario
    public function permisos()
    {
        return $this->hasMany(Permission::class);
    }

    // Relación con los permisos que ha aprobado el usuario
    public function permisosAprobados()
    {
        return $this->hasMany(Permission::class, 'approved_by');
    }

    // Relación con los permisos en los que Recursos Humanos ha intervenido
    public function permisosRH()
    {
        return $this->hasMany(Permission::class, 'hr_id');
    }
    // En el modelo User
    public function departamento()
    {
    return $this->belongsTo(Department::class);
    }
    public function vacaciones()
{
    return $this->hasMany(SolicitudVacacion::class);
}
public function getIsJefeAttribute()
{
    return $this->role === 'jefe'; // O como esté definido en tu sistema
}

}
