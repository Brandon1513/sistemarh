<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Para los cumpleaños. Solo se publica el día y el mes, nunca el año.
            $table->date('fecha_nacimiento')->nullable()->after('fecha_ingreso');
            // diurno | nocturno (para la lista de cumpleañeros del mes)
            $table->string('turno', 20)->nullable()->after('puesto_empleado');
            // La foto de credencial que pone RH (la de perfil es la de WhatsApp)
            $table->string('foto_oficial')->nullable()->after('foto_perfil');
            // RH la desmarca si alguien pide que no publiquen su cumpleaños o aniversario
            $table->boolean('incluir_celebraciones')->default(true)->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fecha_nacimiento', 'turno', 'foto_oficial', 'incluir_celebraciones']);
        });
    }
};