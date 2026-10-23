<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        DB::table('sedes')->insert([
            ['nombre' => 'Guadalajara', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'CDMX',        'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Monterrey',   'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sedes');
    }
};