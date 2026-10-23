<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudPermisoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'tipo'                  => 'permiso',
            'titulo'                => $this->tipo, // Permiso, Comisión o Suspensión
            'estado'                => $this->estado,
            'fecha_inicio'          => Carbon::parse($this->fecha_inicio)->toDateString(),
            'fecha_fin'             => Carbon::parse($this->fecha_termino)->toDateString(),
            'fecha_regreso_laborar' => $this->fecha_regreso_laborar ? Carbon::parse($this->fecha_regreso_laborar)->toDateString() : null,
            'goce'                  => $this->tipo_permiso,
            'motivo'                => $this->motivo,
            'tiene_archivo'         => (bool) $this->archivo,
            'creada_en'             => $this->created_at?->toIso8601String(),
            'puede_cancelar'        => $this->estado === 'pendiente' && $request->user()?->can('delete', $this->resource),
            'archivo_url' => $this->archivo ? asset('storage/' . $this->archivo) : null,
            'empleado' => $this->whenLoaded('empleado', fn () => $this->empleado ? [
                'id'       => $this->empleado->id,
                'name'     => $this->empleado->name,
                'puesto'   => $this->empleado->puesto_empleado,
                'foto_url' => $this->empleado->foto_perfil ? asset('storage/' . $this->empleado->foto_perfil) : null,
            ] : null),
        ];
    }
}