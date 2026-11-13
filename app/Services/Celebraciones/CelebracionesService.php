<?php

namespace App\Services\Celebraciones;

use App\Models\Comunicado;
use App\Models\User;
use App\Services\CorreoComunicados;
use App\Services\PushService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Publica las celebraciones automáticas: para toda la empresa, con correo al grupo y push a todos
 * (según config/celebraciones.php), y una felicitación personal por push a quien celebra.
 */
class CelebracionesService
{
    public function __construct(
        private GeneradorTarjetas $generador,
        private PushService $push,
        private CorreoComunicados $correos,
    ) {
    }

    /** Los del día anterior se archivan: siguen en la pestaña de Celebraciones, pero ya no en el inicio. */
    public function archivarAnteriores(Carbon $dia): int
    {
        return Comunicado::whereIn('celebracion', ['cumpleanos', 'aniversario'])
            ->where('estado', 'publicado')
            ->whereDate('celebracion_fecha', '<', $dia->toDateString())
            ->update(['estado' => 'archivado', 'fijado' => false]);
    }

    public function publicarCumpleanos(User $persona, Carbon $dia): ?Comunicado
    {
        if ($this->yaSePublico('cumpleanos', $persona, $dia)) {
            return null;
        }

        $nombre = GeneradorTarjetas::primerNombre($persona->name);
        $puesto = $persona->puesto_empleado ? ", {$persona->puesto_empleado}" : '';

        $comunicado = $this->crear('cumpleanos', [
            'tipo'              => 'cumpleanos',
            'celebracion'       => 'cumpleanos',
            'celebrado_id'      => $persona->id,
            'celebracion_fecha' => $dia,
            'titulo'            => "¡Feliz cumpleaños, {$nombre}!",
            'resumen'           => "Hoy celebramos el cumpleaños de {$persona->name}. ¡Mándale una felicitación!",
            'contenido'         => "Hoy celebramos el cumpleaños de **{$persona->name}**{$puesto}.\n\nEn este día tan especial le deseamos que todos sus sueños se cumplan y que sea un año lleno de felicidad. ¡Mándale una felicitación! 🎂",
            'imagen'            => $this->generador->cumpleanos($persona, $dia),
        ]);

        $this->felicitar($persona, "🎂 ¡Feliz cumpleaños, {$nombre}!", 'Todo el equipo Dasavena te desea un día increíble.', $comunicado);
        $this->difundir($comunicado, 'cumpleanos', "🎂 Hoy cumple años {$nombre}", $persona->id);

        return $comunicado;
    }

    public function publicarAniversario(User $persona, Carbon $dia): ?Comunicado
    {
        if ($this->yaSePublico('aniversario', $persona, $dia)) {
            return null;
        }

        $nombre = GeneradorTarjetas::primerNombre($persona->name);
        $anios = $persona->aniosEnLaEmpresa($dia);
        $textoAnios = $anios . ($anios === 1 ? ' año' : ' años');
        $puesto = $persona->puesto_empleado ? ", {$persona->puesto_empleado}," : '';

        $comunicado = $this->crear('aniversario', [
            'tipo'              => 'aniversario',
            'celebracion'       => 'aniversario',
            'celebrado_id'      => $persona->id,
            'celebracion_fecha' => $dia,
            'titulo'            => "¡{$textoAnios} con nosotros, {$nombre}!",
            'resumen'           => "Hoy {$persona->name} cumple {$textoAnios} en Dasavena. ¡Gracias por ser parte de nuestra historia!",
            'contenido'         => "Hoy **{$persona->name}**{$puesto} cumple **{$textoAnios}** en Dasavena.\n\nNos llena de orgullo celebrar un año más de su colaboración y tenacidad. ¡Gracias por ser parte de nuestra historia! 🎉",
            'imagen'            => $this->generador->aniversario($persona, $dia),
        ]);

        $this->felicitar($persona, "🎉 ¡Felicidades por tus {$textoAnios} en Dasavena!", 'Gracias por ser parte de nuestra historia.', $comunicado);
        $this->difundir($comunicado, 'aniversario', "🏅 {$nombre} cumple {$textoAnios} en Dasavena", $persona->id);

        return $comunicado;
    }

    /**
     * Cumpleañeros del mes, por turno. Con $forzar, se vuelve a generar aunque ya exista
     * (pero no vuelve a mandar el correo ni la push, para no repetirlos).
     */
    public function publicarListaMes(Carbon $mes, bool $forzar = false): ?Comunicado
    {
        $inicio = $mes->copy()->startOfMonth();

        $existente = Comunicado::where('celebracion', 'lista_mes')->whereDate('celebracion_fecha', $inicio->toDateString())->first();
        if ($existente && ! $forzar) {
            return null;
        }
        $yaSeAviso = (bool) $existente?->correo_enviado_en;
        $existente?->forceDelete();

        $personas = User::where('incluir_celebraciones', true)
            ->whereNotNull('fecha_nacimiento')
            ->whereMonth('fecha_nacimiento', $inicio->month)
            ->get()
            ->sortBy(fn ($u) => str_pad($u->fecha_nacimiento->day, 2, '0', STR_PAD_LEFT) . ' ' . $u->name);

        if ($personas->isEmpty()) {
            return null;
        }

        $titulos = ['diurno' => 'TURNO DIURNO', 'nocturno' => 'TURNO NOCTURNO'];
        $secciones = $personas->groupBy(fn ($u) => $titulos[$u->turno] ?? 'OTROS')
            ->sortKeys()
            ->map(fn ($grupo) => $grupo->map(fn ($u) => [
                'dia'    => $u->fecha_nacimiento->day,
                'nombre' => mb_strtoupper($u->name),
                'puesto' => mb_strtoupper((string) $u->puesto_empleado),
            ])->values()->all())
            ->all();

        $nombreMes = $inicio->copy()->locale('es')->isoFormat('MMMM');

        // El texto (para quien no ve la imagen): día y nombre, nunca el año
        $texto = collect($secciones)->map(function ($filas, $titulo) {
            return '**' . mb_convert_case($titulo, MB_CASE_TITLE) . "**\n"
                . collect($filas)->map(fn ($f) => "{$f['dia']} · " . mb_convert_case($f['nombre'], MB_CASE_TITLE) . ($f['puesto'] ? ' — ' . mb_convert_case($f['puesto'], MB_CASE_TITLE) : ''))->implode("\n");
        })->implode("\n\n");

        // La lista del mes anterior se archiva
        Comunicado::where('celebracion', 'lista_mes')->where('estado', 'publicado')->update(['estado' => 'archivado', 'fijado' => false]);

        $comunicado = $this->crear('lista_mes', [
            'tipo'              => 'cumpleanos',
            'celebracion'       => 'lista_mes',
            'celebracion_fecha' => $inicio,
            'titulo'            => "🎂 Cumpleañeros de {$nombreMes}",
            'resumen'           => "Conoce a quienes cumplen años en {$nombreMes} y felicítalos en su día. 🎉",
            'contenido'         => "¡Estos son los cumpleañeros de {$nombreMes}! Feliz cumpleaños a todos. 🎉\n\n{$texto}",
            'imagen'            => $this->generador->listaMes($inicio, $secciones),
        ]);

        if (! $yaSeAviso) {
            $this->difundir($comunicado, 'lista_mes', "🎂 Los cumpleañeros de {$nombreMes}");
        }

        return $comunicado;
    }

    // ─── Ayudas ──────────────────────────────────────────────────────────────

    private function yaSePublico(string $celebracion, User $persona, Carbon $dia): bool
    {
        return Comunicado::withTrashed()
            ->where('celebracion', $celebracion)
            ->where('celebrado_id', $persona->id)
            ->whereDate('celebracion_fecha', $dia->toDateString())
            ->exists();
    }

    private function crear(string $clave, array $datos): Comunicado
    {
        $avisos = config("celebraciones.avisos.{$clave}", []);

        return Comunicado::create($datos + [
            'firma'          => 'Recursos Humanos',
            'estado'         => 'publicado',
            'publicado_en'   => now(),
            'enviar_push'    => (bool) ($avisos['push_a_todos'] ?? false),
            'enviar_correo'  => ($avisos['correo'] ?? false) ? 'automatico' : 'ninguno',
            'usar_plantilla' => false,
        ]);
    }

    /**
     * La push a todos (menos a quien celebra, que ya recibió la suya) y el correo al grupo de toda la empresa.
     * Si algo falla, la celebración queda publicada igual.
     */
    private function difundir(Comunicado $comunicado, string $clave, string $tituloPush, ?int $excluir = null): void
    {
        $avisos = config("celebraciones.avisos.{$clave}", []);

        if ($avisos['push_a_todos'] ?? false) {
            try {
                $this->push->enviarAMuchos(
                    $comunicado->destinatarios()->when($excluir, fn ($q) => $q->where('users.id', '!=', $excluir)),
                    $tituloPush,
                    Str::limit($comunicado->resumen_corto, 120),
                    ['destino' => 'comunicado', 'id' => $comunicado->id],
                );
            } catch (\Throwable $e) {
                Log::warning('No se pudo enviar la push de la celebración', ['comunicado' => $comunicado->id, 'error' => $e->getMessage()]);
            }
        }

        if ($avisos['correo'] ?? false) {
            try {
                $this->correos->enviar($comunicado);
            } catch (\Throwable $e) {
                Log::warning('No se pudo preparar el correo de la celebración', ['comunicado' => $comunicado->id, 'error' => $e->getMessage()]);
            }
        }
    }

    /** La felicitación personal, solo a quien celebra. */
    private function felicitar(User $persona, string $titulo, string $cuerpo, Comunicado $comunicado): void
    {
        try {
            $this->push->enviar($persona, $titulo, $cuerpo, ['destino' => 'comunicado', 'id' => $comunicado->id]);
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar la felicitación', ['usuario' => $persona->id, 'error' => $e->getMessage()]);
        }
    }
}