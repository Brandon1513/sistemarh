<?php

namespace App\Policies;

use App\Models\SolicitudVacacion;
use App\Models\User;
use App\Policies\Concerns\AutorizaSolicitudes;

class SolicitudVacacionPolicy
{
    use AutorizaSolicitudes;

    public function view(User $user, SolicitudVacacion $solicitud): bool
    {
        return $this->puedeVer($user, $solicitud->empleado);
    }

    public function approve(User $user, SolicitudVacacion $solicitud): bool
    {
        return $this->puedeResolver($user, $solicitud->empleado);
    }

    public function reject(User $user, SolicitudVacacion $solicitud): bool
    {
        return $this->puedeResolver($user, $solicitud->empleado);
    }

    public function delete(User $user, SolicitudVacacion $solicitud): bool
    {
        return $this->puedeCancelar($user, $solicitud->empleado_id);
    }
}