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
        Schema::create('solicitudes_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('users');
            $table->foreignId('departamento_id')->constrained('departments');
            $table->date('fecha_inicio');
            $table->date('fecha_termino');
            $table->string('motivo');
            $table->string('tipo_permiso');  // Con Goce de Sueldo, Sin Goce de Sueldo
            $table->string('estado')->default('pendiente');  // pendiente, aprobado, rechazado
            $table->date('fecha_regreso_laborar')->nullable();  // Fecha opcional
            $table->enum('tipo', ['Permiso', 'Comisión', 'Suspensión'])->default('Permiso');  // Tipo de solicitud
            $table->string('dia_descanso')->nullable();  // Día de descanso opcional
            $table->timestamps();  // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_permisos');
    }
};
