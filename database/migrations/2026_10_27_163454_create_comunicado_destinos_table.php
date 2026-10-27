<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A quién va dirigido cada comunicado. Sin filas = para todos.
        Schema::create('comunicado_destinos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunicado_id')->constrained('comunicados')->cascadeOnDelete();
            $table->string('tipo', 20);                  // sede | departamento | grupo | usuario
            $table->unsignedBigInteger('destino_id');
            $table->unique(['comunicado_id', 'tipo', 'destino_id']);
            $table->index(['tipo', 'destino_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicado_destinos');
    }
};