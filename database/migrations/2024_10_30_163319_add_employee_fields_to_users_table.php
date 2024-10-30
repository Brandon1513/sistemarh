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
        Schema::table('users', function (Blueprint $table) {

            //
            $table->string('clave_empleado')->nullable(); // Clave única para el empleado
            $table->date('fecha_ingreso')->nullable();    // Fecha de ingreso
            $table->string('puesto_empleado')->nullable(); // Puesto del empleado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('clave_empleado');
            $table->dropColumn('fecha_ingreso');
            $table->dropColumn('puesto_empleado');
        });
    }
};
