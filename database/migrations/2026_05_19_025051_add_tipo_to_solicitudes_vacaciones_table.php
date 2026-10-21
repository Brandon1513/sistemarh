<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes_vacaciones', function (Blueprint $table) {
            // tipo: 'normal' = solicitada por empleado, 'espontanea' = registrada por RH
            $table->enum('tipo', ['normal', 'espontanea'])->default('normal')->after('estado');
            // quién la creó (null = el propio empleado, id = usuario de RH que la registró)
            $table->foreignId('creado_por')->nullable()->after('tipo')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_vacaciones', function (Blueprint $table) {
            $table->dropForeign(['creado_por']);
            $table->dropColumn(['tipo', 'creado_por']);
        });
    }
};