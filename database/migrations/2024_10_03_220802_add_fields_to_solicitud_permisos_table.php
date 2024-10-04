<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('solicitudes_permisos', function (Blueprint $table) {
            $table->date('fecha_regreso_laborar')->nullable(); // Fecha de regreso a laborar
            $table->enum('tipo', ['Permiso', 'Comisión', 'Suspensión'])->default('Permiso'); // Opciones de tipo de permiso
            $table->string('dia_descanso')->nullable(); // Día de descanso
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes_permisos', function (Blueprint $table) {
            $table->dropColumn('fecha_regreso_laborar');
            $table->dropColumn('tipo');
            $table->dropColumn('dia_descanso');
        });
    }
};
