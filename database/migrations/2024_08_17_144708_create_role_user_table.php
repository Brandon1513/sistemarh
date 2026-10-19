<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Migración obsoleta: creaba la tabla pivote `role_user`, reemplazada por
 * `model_has_roles` de Spatie (create_permission_tables).
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
