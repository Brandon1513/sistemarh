<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $fecha = Carbon::parse($this->date)->toDateString();

        return [
            'id'              => $this->id,
            'tipo'            => 'pase',
            'titulo'          => $this->entry_exit_type === 'entrada' ? 'Pase de entrada' : 'Pase de salida',
            'estado'          => $this->status, // en esta tabla la columna se llama status
            'fecha_inicio'    => $fecha,
            'fecha_fin'       => $fecha,
            'movimiento'      => $this->entry_exit_type,
            'horario_oficial' => substr((string) $this->official_schedule, 0, 5),
            'hora'            => substr((string) $this->entry_exit_time, 0, 5),
            'motivo'          => $this->reason,
            'tiene_archivo'   => (bool) $this->supporting_document,
            'creada_en'       => $this->created_at?->toIso8601String(),
            'puede_cancelar'  => $this->status === 'pendiente' && $request->user()?->can('delete', $this->resource),
            'archivo_url' => $this->supporting_document ? asset('storage/' . $this->supporting_document) : null,
            'empleado' => $this->whenLoaded('user', fn () => $this->user ? [
                'id'       => $this->user->id,
                'name'     => $this->user->name,
                'puesto'   => $this->user->puesto_empleado,
                'foto_url' => $this->user->foto_perfil ? asset('storage/' . $this->user->foto_perfil) : null,
            ] : null),
        ];
    }
}