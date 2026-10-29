<?php

namespace App\Services;

use App\Jobs\EnviarCorreoComunicado;
use App\Models\Comunicado;
use App\Models\Grupo;
use App\Models\Sede;
use Illuminate\Support\Str;

/**
 * Decide a dónde va el correo de un comunicado: a un grupo de Microsoft 365 (un solo envío)
 * cuando la audiencia coincide exactamente, o a cada persona con correo de Dasavena.
 */
class CorreoComunicados
{
    /** Personas por correo, en copia oculta. Graph acepta hasta 500, pero 50 es más seguro contra el filtro de spam. */
    private const POR_BLOQUE = 50;

    public static function remitentePara(Comunicado $comunicado): array
    {
        $remitentes = config('comunicados.remitentes');

        return $comunicado->tipo === 'aviso' ? $remitentes['aviso'] : $remitentes['otros'];
    }

    /**
     * El plan de envío para unos destinos ([['tipo' => 'sede', 'destino_id' => 2], ...]).
     *  - ['tipo' => 'grupo', 'direccion' => 'equipodasavena@...', 'nombre' => 'toda la empresa']
     *  - ['tipo' => 'personas', 'direcciones' => ['juan@dasavena.com', ...]]
     */
    public function plan(array $destinos): array
    {
        // Para todos: el grupo de toda la empresa
        if ($destinos === [] && config('comunicados.correo_todos')) {
            return ['tipo' => 'grupo', 'direccion' => config('comunicados.correo_todos'), 'nombre' => 'toda la empresa'];
        }

        // Una sola sede o un solo grupo que tenga su grupo de Microsoft 365
        if (count($destinos) === 1 && in_array($destinos[0]['tipo'], ['sede', 'grupo'], true)) {
            $modelo = $destinos[0]['tipo'] === 'sede'
                ? Sede::find($destinos[0]['destino_id'])
                : Grupo::find($destinos[0]['destino_id']);

            if ($modelo?->correo_grupo) {
                return ['tipo' => 'grupo', 'direccion' => $modelo->correo_grupo, 'nombre' => $modelo->nombre];
            }
        }

        // Cualquier otra combinación: cada persona con correo de Dasavena
        $direcciones = Comunicado::consultaDestinatarios($destinos)
            ->whereNotNull('email')
            ->pluck('email')
            ->map(fn ($e) => strtolower(trim($e)))
            ->filter(fn ($e) => $this->esCorporativo($e))
            ->unique()
            ->values()
            ->all();

        return ['tipo' => 'personas', 'direcciones' => $direcciones];
    }

    /** El texto para el formulario: cómo se va a mandar. */
    public function resumen(array $destinos): string
    {
        $plan = $this->plan($destinos);

        if ($plan['tipo'] === 'grupo') {
            return "1 correo al grupo {$plan['direccion']}";
        }

        $n = count($plan['direcciones']);
        $dominios = implode(', @', config('comunicados.dominios'));

        return $n === 0
            ? "Nadie de los elegidos tiene correo @{$dominios}"
            : "{$n} " . ($n === 1 ? 'correo' : 'correos') . " a quienes tienen correo @{$dominios}";
    }

    /**
     * Pone en la cola los correos de un comunicado. Regresa cuántos envíos se programaron.
     */
    public function enviar(Comunicado $comunicado): int
    {
        $plan = $this->plan($comunicado->destinos()->get(['tipo', 'destino_id'])->toArray());
        $remitente = self::remitentePara($comunicado);
        $envios = 0;

        if ($plan['tipo'] === 'grupo') {
            EnviarCorreoComunicado::dispatch($comunicado->id, [$plan['direccion']]);
            $envios = 1;
        } else {
            // El "Para" es el propio buzón y las personas van en copia oculta: nadie ve los correos de los demás
            foreach (array_chunk($plan['direcciones'], self::POR_BLOQUE) as $bloque) {
                EnviarCorreoComunicado::dispatch($comunicado->id, [$remitente['correo']], $bloque);
                $envios++;
            }
        }

        $comunicado->update(['correo_enviado_en' => now(), 'remitente' => $remitente['correo']]);

        return $envios;
    }

    private function esCorporativo(string $correo): bool
    {
        return collect(config('comunicados.dominios'))
            ->contains(fn ($dominio) => Str::endsWith($correo, '@' . strtolower($dominio)));
    }
}