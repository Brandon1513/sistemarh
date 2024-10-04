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
            $table->unsignedBigInteger('empleado_id');
            $table->unsignedBigInteger('departamento_id');
            $table->date('fecha_inicio');
            $table->date('fecha_termino');
            $table->text('motivo');
            $table->string('tipo_permiso'); // 'Con Goce de Sueldo' o 'Sin Goce de Sueldo'
            $table->string('estado')->default('Pendiente'); // 'Pendiente', 'Aprobado', 'Rechazado'
            $table->timestamps();
    
            // Claves foráneas
            $table->foreign('empleado_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('departamento_id')->references('id')->on('departments')->onDelete('cascade');
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
