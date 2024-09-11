<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // Incluye el trait HasRoles de Spatie

    protected $fillable = [
        'name',
        'email',
        'password',
        'supervisor_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
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
}
