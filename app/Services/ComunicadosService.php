<?php

namespace App\Services;

use App\Models\Comunicado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ComunicadosService
{
    /** Cómo aparece el autor si no se escribe otra firma. */
    public static function firmaPorDefecto(?string $tipo): string
    {
        return $tipo === 'aviso' ? 'Recursos Humanos' : 'Marketing';
    }

    /**
     * Crea o actualiza un comunicado con sus imágenes. Si $datos['accion'] es "publicar", lo publica.
     */
    public function guardar(Comunicado $comunicado, array $datos, Request $request): Comunicado
    {
        return DB::transaction(function () use ($comunicado, $datos, $request) {
            $tipo = $datos['tipo'];

            $comunicado->fill([
                'tipo'      => $tipo,
                'categoria' => $tipo === 'aviso' ? ($datos['categoria'] ?? 'general') : null,
                'titulo'    => trim($datos['titulo']),
                'resumen'   => $datos['resumen'] ?? null,
                'contenido' => $datos['contenido'] ?? null,
                'enlace'    => $datos['enlace'] ?? null,
                'firma'     => ($datos['firma'] ?? null) ?: self::firmaPorDefecto($tipo),
                'fijado'    => (bool) ($datos['fijado'] ?? false),
            ]);

            if (! $comunicado->exists) {
                $comunicado->estado = 'borrador';
            }

            // Imagen principal: quitarla o reemplazarla
            if (! empty($datos['quitar_imagen']) && $comunicado->imagen) {
                $this->borrarArchivo($comunicado->imagen);
                $comunicado->imagen = null;
            }

            if ($request->hasFile('imagen')) {
                if ($comunicado->imagen) {
                    $this->borrarArchivo($comunicado->imagen);
                }
                $comunicado->imagen = $request->file('imagen')->store('comunicados', 'public');
            }

            $comunicado->save();

            // Galería: quitar las marcadas y agregar las nuevas al final
            if (! empty($datos['quitar_galeria'])) {
                $comunicado->imagenes()->whereIn('id', $datos['quitar_galeria'])->get()
                    ->each(function ($foto) {
                        $this->borrarArchivo($foto->imagen);
                        $foto->delete();
                    });
            }

            $orden = (int) $comunicado->imagenes()->max('orden');
            foreach ($request->file('galeria', []) as $archivo) {
                $comunicado->imagenes()->create([
                    'imagen' => $archivo->store('comunicados/galeria', 'public'),
                    'orden'  => ++$orden,
                ]);
            }

            if ($datos['accion'] === 'publicar' && $comunicado->estado !== 'publicado') {
                $this->publicar($comunicado);
            }

            return $comunicado;
        });
    }

    /**
     * Lo hace visible para los empleados. En la fase 3, aquí se mandarán la push y el correo.
     */
    public function publicar(Comunicado $comunicado): void
    {
        $comunicado->update([
            'estado'       => 'publicado',
            // Si se vuelve a publicar uno archivado, conserva su fecha original
            'publicado_en' => $comunicado->publicado_en ?? now(),
            'publicar_en'  => null,
        ]);
    }

    /** Lo oculta para los empleados, sin borrarlo. */
    public function archivar(Comunicado $comunicado): void
    {
        $comunicado->update(['estado' => 'archivado', 'fijado' => false]);
    }

    private function borrarArchivo(string $ruta): void
    {
        Storage::disk('public')->delete($ruta);
    }
}