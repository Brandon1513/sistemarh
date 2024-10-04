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
        Schema::dropIfExists('solicitudes_permisos');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('solicitudes_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('users');
            $table->foreignId('departamento_id')->constrained('departments');
            $table->date('fecha_inicio');
            $table->date('fecha_termino');
            $table->string('motivo');
            $table->string('tipo_permiso');
            $table->string('estado')->default('pendiente');
            $table->date('fecha_regreso_laborar')->nullable();
            $table->enum('tipo', ['Permiso', 'Comisión', 'Suspensión'])->default('Permiso');
            $table->string('dia_descanso')->nullable();
            $table->timestamps();
        });
    }
};
