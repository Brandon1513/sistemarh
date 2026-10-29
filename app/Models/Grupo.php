<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'creado_por', 'activo', 'correo_grupo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'grupo_usuario');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}