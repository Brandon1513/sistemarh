<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'clave_empleado'  => $this->clave_empleado,
            'puesto_empleado' => $this->puesto_empleado,
            'fecha_ingreso'   => $this->fecha_ingreso?->toDateString(),
            'departamento'    => $this->departamento ? ['id' => $this->departamento->id, 'name' => $this->departamento->name] : null,
            'supervisor'      => $this->supervisor ? ['id' => $this->supervisor->id, 'name' => $this->supervisor->name] : null,
            'roles'           => $this->getRoleNames(),
            // La app usa esto para mostrar u ocultar la bandeja de aprobaciones
            'es_jefe'         => $this->hasRole('jefe') || $this->subordinates()->exists(),
            'foto_url' => $this->foto_perfil ? asset('storage/' . $this->foto_perfil) : null,
        ];
    }
}