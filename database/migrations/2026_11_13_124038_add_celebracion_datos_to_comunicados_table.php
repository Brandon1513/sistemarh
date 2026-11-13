<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comunicados', function (Blueprint $table) {
            // Los datos del festejo (versión, turno, fecha, hora, lugar...), para poder regenerarlo
            $table->json('celebracion_datos')->nullable()->after('celebracion_fecha');
        });
    }

    public function down(): void
    {
        Schema::table('comunicados', function (Blueprint $table) {
            $table->dropColumn('celebracion_datos');
        });
    }
};