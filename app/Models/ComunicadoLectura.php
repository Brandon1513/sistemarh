<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComunicadoLectura extends Model
{
    public $timestamps = false;

    protected $table = 'comunicado_lecturas';

    protected $fillable = ['comunicado_id', 'user_id', 'canal', 'leido_en'];

    protected function casts(): array
    {
        return ['leido_en' => 'datetime'];
    }

    public function comunicado()
    {
        return $this->belongsTo(Comunicado::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}