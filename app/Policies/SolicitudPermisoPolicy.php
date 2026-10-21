<?php

namespace App\Policies;

use App\Models\SolicitudPermiso;
use App\Models\User;
use App\Policies\Concerns\AutorizaSolicitudes;

class SolicitudPermisoPolicy
{
    use AutorizaSolicitudes;

    public function view(User $user, SolicitudPermiso $solicitud): bool
    {
        return $this->puedeVer($user, $solicitud->empleado);
    }

    public function approve(User $user, SolicitudPermiso $solicitud): bool
    {
        return $this->puedeResolver($user, $solicitud->empleado);
    }

    public function reject(User $user, SolicitudPermiso $solicitud): bool
    {
        return $this->puedeResolver($user, $solicitud->empleado);
    }

    public function delete(User $user, SolicitudPermiso $solicitud): bool
    {
        return $this->puedeCancelar($user, $solicitud->empleado_id);
    }
}