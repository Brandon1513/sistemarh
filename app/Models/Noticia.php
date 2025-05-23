<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'contenido',
        'imagen',
        'autor',
        'fecha',
        'activo',
        'multimedia',
        'tipo_multimedia',
    ];
    public function galeria()
    {
        return $this->hasMany(NoticiaGaleria::class);
    }
}
