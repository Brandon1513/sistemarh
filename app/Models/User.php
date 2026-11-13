<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens; // Incluye el trait HasRoles de Spatie

    protected $fillable = [
        'name',
        'email',
        'password',
        'activo',
        'supervisor_id',
        'clave_empleado',
        'fecha_ingreso',
        'puesto_empleado',
        'departamento_id',
        'foto_perfil',
        'sede_id',
        'fecha_nacimiento',
        'turno',
        'foto_oficial',
        'incluir_celebraciones',

    ];

    protected $hidden = [
        'password',
        'remember_token',
        'fcm_token',
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
        'fecha_nacimiento'      => 'date',
        'fecha_ingreso'         => 'date',
        'incluir_celebraciones' => 'boolean',
    ];

    // Conversión de fechas a objetos Carbon
    protected $dates = [
        'fecha_ingreso',
    ];

    public const TURNOS = [
        'diurno'   => 'Turno diurno',
        'nocturno' => 'Turno nocturno',
    ];

    public function getFotoOficialUrlAttribute(): ?string
    {
        return $this->foto_oficial ? asset('storage/' . $this->foto_oficial) : null;
    }

    /**
     * Los que cumplen años ese día (y que RH dejó incluidos en las celebraciones).
     * Los del 29 de febrero celebran el 28 en los años que no son bisiestos.
     */
    public function scopeCumpleanosEl(\Illuminate\Database\Eloquent\Builder $query, \Illuminate\Support\Carbon $dia): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('incluir_celebraciones', true)
            ->whereNotNull('fecha_nacimiento')
            ->where(function ($w) use ($dia) {
                $w->where(fn($x) => $x->whereMonth('fecha_nacimiento', $dia->month)->whereDay('fecha_nacimiento', $dia->day));

                if ($dia->month === 2 && $dia->day === 28 && ! $dia->isLeapYear()) {
                    $w->orWhere(fn($x) => $x->whereMonth('fecha_nacimiento', 2)->whereDay('fecha_nacimiento', 29));
                }
            });
    }

    /**
     * Los que cumplen años en la empresa ese día (al menos uno: el día que entraron no cuenta).
     */
    public function scopeAniversarioEl(\Illuminate\Database\Eloquent\Builder $query, \Illuminate\Support\Carbon $dia): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('incluir_celebraciones', true)
            ->whereNotNull('fecha_ingreso')
            ->whereYear('fecha_ingreso', '<', $dia->year)
            ->where(function ($w) use ($dia) {
                $w->where(fn($x) => $x->whereMonth('fecha_ingreso', $dia->month)->whereDay('fecha_ingreso', $dia->day));

                if ($dia->month === 2 && $dia->day === 28 && ! $dia->isLeapYear()) {
                    $w->orWhere(fn($x) => $x->whereMonth('fecha_ingreso', 2)->whereDay('fecha_ingreso', 29));
                }
            });
    }

    /** Cuántos años cumple en la empresa ese día. */
    public function aniosEnLaEmpresa(\Illuminate\Support\Carbon $dia): int
    {
        return $this->fecha_ingreso
            ? (int) floor($this->fecha_ingreso->diffInYears($dia->copy()->endOfDay()))
            : 0;
    }
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

    public function dispositivos()
    {
        return $this->hasMany(UserDeviceToken::class);
    }
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_usuario');
    }
}
