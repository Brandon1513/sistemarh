<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaInhabil extends Model
{
    protected $table = 'dias_inhabiles';

    protected $fillable = ['sede_id', 'nombre', 'mes', 'dia', 'anio'];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    protected static function booted(): void
    {
        static::saved(fn () => \App\Support\CalendarioLaboral::limpiarCache());
        static::deleted(fn () => \App\Support\CalendarioLaboral::limpiarCache());
    }
}