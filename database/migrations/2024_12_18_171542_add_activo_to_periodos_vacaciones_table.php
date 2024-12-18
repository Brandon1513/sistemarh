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
        Schema::table('periodos_vacaciones', function (Blueprint $table) {
            $table->boolean('activo')->default(true); // Por defecto estará activo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periodos_vacaciones', function (Blueprint $table) {
            $table->dropColumn('activo');
        });
    }
};
