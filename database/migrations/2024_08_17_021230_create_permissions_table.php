<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Migración obsoleta: creaba una tabla `permissions` propia, reemplazada por
 * employee_permissions y por la tabla `permissions` de Spatie (create_permission_tables).
 * Se deja vacía para que la base pueda migrarse desde cero; en producción ya está
 * registrada y no se vuelve a ejecutar.
 */
return new class extends Migration
{
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }
};
