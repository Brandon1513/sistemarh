<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComunicadoDestino extends Model
{
    public const TIPOS = ['sede', 'departamento', 'grupo', 'usuario'];

    public $timestamps = false;

    protected $table = 'comunicado_destinos';

    protected $fillable = ['comunicado_id', 'tipo', 'destino_id'];

    public function comunicado()
    {
        return $this->belongsTo(Comunicado::class);
    }
}