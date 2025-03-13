<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('noticias', function (Blueprint $table) {
        $table->string('tipo_multimedia')->default('imagen'); // 'imagen', 'video', 'youtube'
        $table->string('multimedia')->nullable(); // Guarda la URL del archivo o del enlace
    });
}

public function down()
{
    Schema::table('noticias', function (Blueprint $table) {
        $table->dropColumn('tipo_multimedia');
        $table->dropColumn('multimedia');
    });
}

};
