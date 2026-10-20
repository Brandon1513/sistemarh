<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait AutorizaSolicitudes
{
    /** Roles que pueden aprobar cualquier solicitud además del jefe directo. */
    protected array $rolesAprobadoresGlobales = ['administrador'];

    /** Roles que pueden consultar cualquier solicitud. */
    protected array $rolesConsulta = ['recursos_humanos', 'administrador'];

    protected function puedeVer(User $user, ?User $empleado): bool
    {
        if ($user->hasAnyRole($this->rolesConsulta)) {
            return true;
        }

        return $empleado !== null
            && ($user->id === $empleado->id || $empleado->supervisor_id === $user->id);
    }
    
    protected function puedeResolver(User $user, ?User $empleado): bool
    {
        if ($empleado === null || $user->id === $empleado->id) {
            return false;
        }

        return $empleado->supervisor_id === $user->id
            || $user->hasAnyRole($this->rolesAprobadoresGlobales);
    }

    protected function puedeCancelar(User $user, int $empleadoId): bool
    {
        return $user->id === $empleadoId;
    }
}