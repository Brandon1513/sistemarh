<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Relación con los usuarios del departamento
    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    // Relación con los permisos asociados al departamento
    public function permisos()
    {
        return $this->hasMany(Permission::class);
    }
}