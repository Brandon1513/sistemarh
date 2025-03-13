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
        Schema::table('carrusel_images', function (Blueprint $table) {
            $table->string('titulo')->nullable()->after('image');
            $table->text('descripcion')->nullable()->after('titulo');
        });
    }

    public function down()
    {
        Schema::table('carrusel_images', function (Blueprint $table) {
            $table->dropColumn(['titulo', 'descripcion']);
        });
    }
};
