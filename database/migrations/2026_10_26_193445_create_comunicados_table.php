<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicados', function (Blueprint $table) {
            $table->id();

            // Qué es
            $table->string('tipo', 20)->index();             // noticia | red_social | aviso | cumpleanos
            $table->string('categoria', 30)->nullable();     // nomina, aguinaldo, ptu, general... (avisos de RH)
            $table->string('titulo');
            $table->string('resumen', 300)->nullable();
            $table->longText('contenido')->nullable();
            $table->string('imagen')->nullable();
            $table->string('enlace')->nullable();            // publicación de red social, video, sitio externo

            // Quién
            $table->foreignId('autor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('firma')->nullable();             // cómo aparece el autor: "Recursos Humanos", "Marketing"

            // Ciclo de vida
            $table->string('estado', 20)->default('borrador')->index(); // borrador | en_revision | devuelto | programado | publicado | archivado
            $table->foreignId('revisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('comentario_revision')->nullable();
            $table->timestamp('revisado_en')->nullable();
            $table->timestamp('publicar_en')->nullable();    // si se programa
            $table->timestamp('publicado_en')->nullable()->index();
            $table->boolean('fijado')->default(false);

            // Canales (fase 3)
            $table->boolean('enviar_push')->default(false);
            $table->string('enviar_correo', 20)->default('ninguno'); // ninguno | usuarios | grupos
            $table->string('remitente')->nullable();

            // De qué noticia vieja viene (solo para la migración de datos)
            $table->unsignedBigInteger('noticia_id')->nullable()->unique();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('comunicado_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunicado_id')->constrained('comunicados')->cascadeOnDelete();
            $table->string('imagen');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicado_imagenes');
        Schema::dropIfExists('comunicados');
    }
};