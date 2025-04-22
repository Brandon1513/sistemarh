<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoticiaGaleria extends Model
{
    use HasFactory;
    protected $table = 'noticia_galeria'; 

    protected $fillable = ['noticia_id', 'imagen'];

    public function noticia()
    {
        return $this->belongsTo(Noticia::class);
    }
}