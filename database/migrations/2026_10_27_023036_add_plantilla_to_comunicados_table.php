<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comunicados', function (Blueprint $table) {
            // Si la imagen se genera con la plantilla de RH a partir del texto
            $table->boolean('usar_plantilla')->default(false)->after('imagen');
            // El letrero rojo de arriba: "AVISO IMPORTANTE", "COMUNICADO"...
            $table->string('encabezado', 60)->nullable()->after('usar_plantilla');
        });
    }

    public function down(): void
    {
        Schema::table('comunicados', function (Blueprint $table) {
            $table->dropColumn(['usar_plantilla', 'encabezado']);
        });
    }
};