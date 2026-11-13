<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comunicados', function (Blueprint $table) {
            // cumpleanos | aniversario | lista_mes (vacío en los comunicados normales)
            $table->string('celebracion', 20)->nullable()->after('tipo')->index();
            // A quién celebra (vacío en la lista del mes)
            $table->foreignId('celebrado_id')->nullable()->after('celebracion')->constrained('users')->nullOnDelete();
            // El día que celebra (o el primer día del mes, en la lista)
            $table->date('celebracion_fecha')->nullable()->after('celebrado_id');
        });
    }

    public function down(): void
    {
        Schema::table('comunicados', function (Blueprint $table) {
            $table->dropConstrainedForeignId('celebrado_id');
            $table->dropColumn(['celebracion', 'celebracion_fecha']);
        });
    }
};