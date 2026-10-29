<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Despertador externo: cron-job.org visita esta ruta cada minuto y aquí se ejecutan
 * las tareas programadas, igual que lo haría el cron del servidor.
 */
class CronController extends Controller
{
    public function __invoke(string $token)
    {
        $esperado = (string) config('app.cron_token');

        // Sin clave configurada, o clave equivocada: como si la ruta no existiera
        abort_if($esperado === '' || ! hash_equals($esperado, $token), 404);

        // Que siga corriendo aunque cron-job.org corte la conexión
        ignore_user_abort(true);
        set_time_limit(55);

        $inicio = microtime(true);
        Artisan::call('schedule:run');
        $salida = trim(Artisan::output());

        // Solo se registra cuando sí hubo algo que ejecutar, para no llenar el log
        if ($salida !== '' && ! str_contains($salida, 'No scheduled commands are ready to run')) {
            Log::info('Tareas programadas ejecutadas', ['salida' => $salida]);
        }

        return response()->json([
            'ok'       => true,
            'segundos' => round(microtime(true) - $inicio, 2),
        ]);
    }
}