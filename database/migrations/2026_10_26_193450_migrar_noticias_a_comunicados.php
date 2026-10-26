<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('noticias')) {
            return;
        }

        DB::transaction(function () {
            DB::table('noticias')->orderBy('id')->each(function ($noticia) {
                $fecha = $noticia->fecha
                    ? \Carbon\Carbon::parse($noticia->fecha)->setTime(9, 0)
                    : \Carbon\Carbon::parse($noticia->created_at);

                $comunicadoId = DB::table('comunicados')->insertGetId([
                    'tipo'         => 'noticia',
                    'titulo'       => $noticia->titulo,
                    'contenido'    => $noticia->contenido,
                    'imagen'       => $noticia->imagen,
                    'enlace'       => $noticia->multimedia,
                    'firma'        => $noticia->autor,
                    // Las noticias activas quedan publicadas; las inactivas, archivadas
                    'estado'       => $noticia->activo ? 'publicado' : 'archivado',
                    'publicado_en' => $fecha,
                    'noticia_id'   => $noticia->id,
                    'created_at'   => $noticia->created_at,
                    'updated_at'   => $noticia->updated_at,
                ]);

                $orden = 0;
                DB::table('noticia_galeria')->where('noticia_id', $noticia->id)->orderBy('id')
                    ->each(function ($foto) use ($comunicadoId, &$orden) {
                        DB::table('comunicado_imagenes')->insert([
                            'comunicado_id' => $comunicadoId,
                            'imagen'        => $foto->imagen,
                            'orden'         => $orden++,
                            'created_at'    => $foto->created_at,
                            'updated_at'    => $foto->updated_at,
                        ]);
                    });
            });
        });
    }

    public function down(): void
    {
        // Las imágenes de la galería se borran solas por el cascadeOnDelete
        DB::table('comunicados')->whereNotNull('noticia_id')->delete();
    }
};