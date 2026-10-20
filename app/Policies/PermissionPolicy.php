<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use App\Policies\Concerns\AutorizaSolicitudes;

class PermissionPolicy
{
    use AutorizaSolicitudes;

    public function view(User $user, Permission $pase): bool
    {
        return $this->puedeVer($user, $pase->user);
    }

    public function approve(User $user, Permission $pase): bool
    {
        return $this->puedeResolver($user, $pase->user);
    }

    public function reject(User $user, Permission $pase): bool
    {
        return $this->puedeResolver($user, $pase->user);
    }

    public function delete(User $user, Permission $pase): bool
    {
        return $this->puedeCancelar($user, $pase->user_id);
    }
}