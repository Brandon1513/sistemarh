<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            // Inactivo: ya no se ofrece para comunicados nuevos, pero los que ya lo usan siguen igual
            $table->boolean('activo')->default(true)->after('descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropColumn('activo');
        });
    }
};