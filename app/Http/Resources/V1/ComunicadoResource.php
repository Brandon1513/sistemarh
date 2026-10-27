<?php

namespace App\Http\Resources\V1;

use App\Models\Comunicado;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComunicadoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'tipo'             => $this->tipo,
            'tipo_nombre'      => $this->tipo_nombre,
            'categoria'        => $this->categoria,
            'categoria_nombre' => $this->categoria ? (Comunicado::CATEGORIAS_AVISO[$this->categoria] ?? null) : null,
            'titulo'           => $this->titulo,
            'resumen'          => $this->resumen_corto,
            'contenido'        => $this->contenido,
            'imagen_url'       => $this->imagen_url,
            'enlace'           => $this->enlace,
            'firma'            => $this->firma,
            'fijado'           => $this->fijado,
            'publicado_en'     => $this->publicado_en?->toIso8601String(),
            'imagenes_count'   => $this->whenCounted('imagenes'),
            'imagenes'         => $this->whenLoaded('imagenes', fn () => $this->imagenes->map->url->values()),
        ];
    }
}