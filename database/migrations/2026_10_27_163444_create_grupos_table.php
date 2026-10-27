<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Grupos de la intranet: "Comité de seguridad", "Jefes de planta"...
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('grupo_usuario', function (Blueprint $table) {
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['grupo_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_usuario');
        Schema::dropIfExists('grupos');
    }
};