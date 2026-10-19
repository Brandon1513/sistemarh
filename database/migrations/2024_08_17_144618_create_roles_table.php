<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Migración obsoleta: creaba una tabla `roles` propia, reemplazada por la de
 * Spatie (create_permission_tables).
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
