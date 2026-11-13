<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Quién abrió cada comunicado. Una fila por persona: la primera vez que lo abrió.
        Schema::create('comunicado_lecturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunicado_id')->constrained('comunicados')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('canal', 10)->default('web'); // web | app
            $table->timestamp('leido_en')->useCurrent();
            $table->unique(['comunicado_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicado_lecturas');
    }
};