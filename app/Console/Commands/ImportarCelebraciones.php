<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportarCelebraciones extends Command
{
    protected $signature = 'celebraciones:importar {archivo : Ruta del CSV} {--simular : Solo revisa, no guarda nada}';

    protected $description = 'Carga fechas de nacimiento y turnos desde el CSV de RH (clave_empleado, fecha_nacimiento, turno)';

    public function handle(): int
    {
        $ruta = $this->argument('archivo');

        if (! is_readable($ruta)) {
            $this->error("No encuentro el archivo: {$ruta}");

            return self::FAILURE;
        }

        $simular = $this->option('simular');
        $archivo = fopen($ruta, 'r');

        // Excel a veces pone una marca invisible al inicio del archivo; se quita
        $encabezados = fgetcsv($archivo);
        $encabezados[0] = preg_replace('/^\xEF\xBB\xBF/', '', $encabezados[0]);
        $encabezados = array_map(fn ($e) => strtolower(trim($e)), $encabezados);

        if (! in_array('clave_empleado', $encabezados) || ! in_array('fecha_nacimiento', $encabezados)) {
            $this->error('El archivo debe tener las columnas clave_empleado y fecha_nacimiento.');

            return self::FAILURE;
        }

        $actualizados = 0;
        $noEncontrados = [];
        $fechasMalas = [];

        while (($fila = fgetcsv($archivo)) !== false) {
            if (count(array_filter($fila)) === 0) {
                continue; // fila vacía
            }

            $datos = array_combine($encabezados, array_pad($fila, count($encabezados), null));
            $clave = trim((string) $datos['clave_empleado']);

            // withoutGlobalScopes: también se busca a los inactivos, por si regresan
            $usuario = User::withoutGlobalScopes()->where('clave_empleado', $clave)->first();
            if (! $usuario) {
                $noEncontrados[] = $clave;
                continue;
            }

            $fecha = $this->leerFecha((string) $datos['fecha_nacimiento']);
            if (! $fecha) {
                $fechasMalas[] = "{$clave} ({$datos['fecha_nacimiento']})";
                continue;
            }

            $cambios = ['fecha_nacimiento' => $fecha];

            $turno = strtolower(trim((string) ($datos['turno'] ?? '')));
            if (array_key_exists($turno, User::TURNOS)) {
                $cambios['turno'] = $turno;
            }

            if (! $simular) {
                $usuario->forceFill($cambios)->save();
            }
            $actualizados++;
        }

        fclose($archivo);

        $this->info(($simular ? '[SIMULACIÓN] Se actualizarían: ' : 'Actualizados: ') . $actualizados);

        if ($noEncontrados) {
            $this->warn('Claves que no existen en la intranet (' . count($noEncontrados) . '): ' . implode(', ', $noEncontrados));
        }
        if ($fechasMalas) {
            $this->warn('Fechas que no se entendieron (' . count($fechasMalas) . '): ' . implode(', ', $fechasMalas));
        }

        return self::SUCCESS;
    }

    /** Acepta 15/03/1990, 15-03-1990 y 1990-03-15. */
    private function leerFecha(string $texto): ?Carbon
    {
        $texto = trim($texto);

        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y'] as $formato) {
            try {
                $fecha = Carbon::createFromFormat($formato, $texto);
                if ($fecha && $fecha->format($formato) === $texto && $fecha->year > 1930 && $fecha->isPast()) {
                    return $fecha->startOfDay();
                }
            } catch (\Throwable) {
                // probar el siguiente formato
            }
        }

        return null;
    }
}