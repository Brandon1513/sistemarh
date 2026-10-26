<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComunicadoImagen extends Model
{
    protected $table = 'comunicado_imagenes';

    protected $fillable = ['comunicado_id', 'imagen', 'orden'];

    public function comunicado()
    {
        return $this->belongsTo(Comunicado::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->imagen);
    }
}