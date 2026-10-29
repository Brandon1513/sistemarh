<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // El grupo de Microsoft 365 que corresponde (equipoguadalajara@dasavena.com...)
        Schema::table('sedes', function (Blueprint $table) {
            $table->string('correo_grupo')->nullable()->after('nombre');
        });

        Schema::table('grupos', function (Blueprint $table) {
            $table->string('correo_grupo')->nullable()->after('descripcion');
        });

        // Para no mandar el correo dos veces
        Schema::table('comunicados', function (Blueprint $table) {
            $table->timestamp('correo_enviado_en')->nullable()->after('remitente');
        });
    }

    public function down(): void
    {
        Schema::table('sedes', fn (Blueprint $t) => $t->dropColumn('correo_grupo'));
        Schema::table('grupos', fn (Blueprint $t) => $t->dropColumn('correo_grupo'));
        Schema::table('comunicados', fn (Blueprint $t) => $t->dropColumn('correo_enviado_en'));
    }
};