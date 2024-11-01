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
        Schema::create('solicitudes_vacaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('users');  // Relación con la tabla users
            $table->foreignId('departamento_id')->constrained('departments');  // Relación con la tabla departamentos
            $table->date('fecha_solicitud');  // Fecha de solicitud
            $table->integer('dias_corresponden');  // Días que corresponden según antigüedad
            $table->integer('dias_solicitados');  // Días que solicita el empleado
            $table->integer('dias_pendientes');  // Días pendientes por disfrutar (dias_corresponden - dias_solicitados)
            $table->string('periodo_correspondiente');  // Período correspondiente (e.g., "2023", "2024")
            $table->date('fecha_inicio');  // Inicio de las vacaciones
            $table->date('fecha_fin');  // Fin de las vacaciones
            $table->date('fecha_reincorporacion');  // Fecha de reincorporación
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');  // Estado de la solicitud
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_vacaciones');
    }
};
