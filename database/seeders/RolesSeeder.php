<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Crear roles
        Role::create(['name' => 'administrador']);
        Role::create(['name' => 'empleado']);
        Role::create(['name' => 'jefe']);
        Role::create(['name' => 'recursos_humanos']);
    }
}
