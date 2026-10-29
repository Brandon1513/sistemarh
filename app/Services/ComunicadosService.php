<?php

namespace App\Services;

use App\Models\Comunicado;
use App\Models\User;
use App\Notifications\ComunicadoRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ComunicadosService
{
    /** Cómo aparece el autor si no se escribe otra firma. */
    public static function firmaPorDefecto(?string $tipo): string
    {
        return $tipo === 'aviso' ? 'Recursos Humanos' : 'Marketing';
    }

    /**
     * "sede:2" → ['tipo' => 'sede', 'destino_id' => 2]. Lo que no tenga ese formato se ignora.
     */
    public static function leerDestinos(array $valores): array
    {
        return collect($valores)
            ->filter(fn ($v) => is_string($v) && preg_match('/^(sede|departamento|grupo|usuario):\d+$/', $v))
            ->unique()
            ->map(function ($v) {
                [$tipo, $id] = explode(':', $v, 2);

                return ['tipo' => $tipo, 'destino_id' => (int) $id];
            })
            ->values()
            ->all();
    }

    /** Las personas de RH que pueden revisar (todas, menos quien pide la revisión). */
    public static function revisoresPara(User $autor)
    {
        return User::role('recursos_humanos')->where('id', '!=', $autor->id)->orderBy('name')->get(['id', 'name']);
    }

    /**
     * Crea o actualiza un comunicado con sus imágenes y destinatarios.
     * $datos['accion']: "borrador", "publicar" o "revision".
     */
    public function guardar(Comunicado $comunicado, array $datos, Request $request): Comunicado
    {
        return DB::transaction(function () use ($comunicado, $datos, $request) {
            $tipo = $datos['tipo'];

            $comunicado->fill([
                'tipo'           => $tipo,
                'categoria'      => $tipo === 'aviso' ? ($datos['categoria'] ?? 'general') : null,
                'titulo'         => trim($datos['titulo']),
                'resumen'        => $datos['resumen'] ?? null,
                'contenido'      => $datos['contenido'] ?? null,
                'enlace'         => $datos['enlace'] ?? null,
                'firma'          => ($datos['firma'] ?? null) ?: self::firmaPorDefecto($tipo),
                'fijado'         => (bool) ($datos['fijado'] ?? false),
                'usar_plantilla' => $tipo === 'aviso' && ! empty($datos['usar_plantilla']),
                'encabezado'     => $tipo === 'aviso' ? ($datos['encabezado'] ?? 'AVISO IMPORTANTE') : null,
            ]);

            if (! $comunicado->exists) {
                $comunicado->estado = 'borrador';
            }

            // Imagen principal: quitarla
            if (! empty($datos['quitar_imagen']) && $comunicado->imagen) {
                $this->borrarArchivo($comunicado->imagen);
                $comunicado->imagen = null;
            }

            // Imagen principal subida a mano: esa manda y la plantilla se apaga
            if ($request->hasFile('imagen')) {
                if ($comunicado->imagen) {
                    $this->borrarArchivo($comunicado->imagen);
                }
                $comunicado->imagen = $request->file('imagen')->store('comunicados', 'public');
                $comunicado->usar_plantilla = false;
            }

            // Imagen que generó el formulario con la plantilla de RH (se reemplaza en cada guardado)
            if ($comunicado->usar_plantilla && $request->hasFile('imagen_generada')) {
                if ($comunicado->imagen) {
                    $this->borrarArchivo($comunicado->imagen);
                }
                $comunicado->imagen = $request->file('imagen_generada')->store('comunicados/avisos', 'public');
            }

            // La plantilla está activada pero no llegó la imagen: avisar en lugar de quedarse callado
            if ($comunicado->usar_plantilla && ! $request->hasFile('imagen_generada') && filled($comunicado->contenido)) {
                session()->flash('warning', 'No se generó la imagen del aviso. Revisa tu conexión y vuelve a guardar, o sube una imagen.');
            }

            $comunicado->save();

            // Destinatarios: se reemplazan completos. "Para todos" = sin destinos.
            $comunicado->destinos()->delete();
            if (($datos['para'] ?? 'todos') === 'elegir') {
                $comunicado->destinos()->createMany(self::leerDestinos($datos['destinos'] ?? []));
            }

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

            if ($datos['accion'] === 'revision') {
                $this->enviarARevision($comunicado, (int) $datos['revisor_id'], $request->user());
            } elseif ($datos['accion'] === 'publicar' && $comunicado->estado !== 'publicado') {
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

    // ─── Revisión ────────────────────────────────────────────────────────────

    /**
     * Lo pone en revisión con una persona de RH y le avisa por correo.
     */
    public function enviarARevision(Comunicado $comunicado, int $revisorId, User $autor): void
    {
        $revisor = self::revisoresPara($autor)->firstWhere('id', $revisorId);

        if (! $revisor) {
            throw ValidationException::withMessages([
                'revisor_id' => 'Elige a una persona de Recursos Humanos para revisarlo (no puedes ser tú).',
            ]);
        }

        $comunicado->update([
            'estado'              => 'en_revision',
            'revisor_id'          => $revisor->id,
            'comentario_revision' => null,
            'revisado_en'         => null,
        ]);

        $this->avisar(User::find($revisor->id), $comunicado, 'pendiente');
    }

    /** El revisor lo aprueba: se publica y se le avisa al autor. */
    public function aprobarRevision(Comunicado $comunicado): void
    {
        $comunicado->update(['revisado_en' => now(), 'comentario_revision' => null]);
        $this->publicar($comunicado);

        $this->avisar($comunicado->autor, $comunicado->fresh(['revisor']), 'aprobado');
    }

    /** El revisor lo devuelve con un comentario: regresa al autor para corregirlo. */
    public function devolver(Comunicado $comunicado, string $comentario): void
    {
        $comunicado->update([
            'estado'              => 'devuelto',
            'comentario_revision' => $comentario,
            'revisado_en'         => now(),
        ]);

        $this->avisar($comunicado->autor, $comunicado->fresh(['revisor']), 'devuelto');
    }

    /** El autor lo retira de la revisión: regresa a borrador. */
    public function retirarRevision(Comunicado $comunicado): void
    {
        $comunicado->update(['estado' => 'borrador', 'revisor_id' => null]);
    }

    /**
     * Los correos de la revisión nunca deben romper el flujo: si fallan, queda en el log.
     */
    private function avisar(?User $persona, Comunicado $comunicado, string $motivo): void
    {
        if (! $persona) {
            return;
        }

        try {
            $persona->notify(new \App\Notifications\ComunicadoRevision($comunicado->loadMissing(['autor', 'revisor']), $motivo));
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar el correo de revisión', ['comunicado' => $comunicado->id, 'error' => $e->getMessage()]);
        }
    }

    private function borrarArchivo(string $ruta): void
    {
        Storage::disk('public')->delete($ruta);
    }
}