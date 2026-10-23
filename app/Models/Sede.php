<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    protected $fillable = ['nombre'];

    public function empleados()
    {
        return $this->hasMany(User::class);
    }

    public function diasInhabiles()
    {
        return $this->hasMany(DiaInhabil::class);
    }
}