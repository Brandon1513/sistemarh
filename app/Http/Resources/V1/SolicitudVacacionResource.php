<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudVacacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $espontanea = $this->tipo === 'espontanea';

        return [
            'id'                    => $this->id,
            'tipo'                  => 'vacaciones',
            'titulo'                => $espontanea ? 'Vacaciones (registradas por RH)' : 'Vacaciones',
            'estado'                => $this->estado,
            'fecha_inicio'          => Carbon::parse($this->fecha_inicio)->toDateString(),
            'fecha_fin'             => Carbon::parse($this->fecha_fin)->toDateString(),
            'fecha_reincorporacion' => $this->fecha_reincorporacion ? Carbon::parse($this->fecha_reincorporacion)->toDateString() : null,
            'dias'                  => $this->dias_solicitados,
            'periodo'               => $this->periodo_correspondiente,
            'es_espontanea'         => $espontanea,
            'creada_en'             => $this->created_at?->toIso8601String(),
            'puede_cancelar'        => $this->estado === 'pendiente' && $request->user()?->can('delete', $this->resource),
        ];
    }
}