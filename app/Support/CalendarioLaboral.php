<?php

namespace App\Support;

use App\Models\DiaInhabil;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Calendario laboral de Dasavena: qué días cuentan como hábiles para vacaciones, permisos y pases.
 *
 * Día de descanso: domingo.
 * Festivos: los de ley (LFT art. 74) más los que otorga la empresa. Los que se
 * mueven cada año (lunes de febrero, marzo y noviembre, Semana Santa) se calculan.
 * Además se suman los días de la tabla `dias_inhabiles`, que RH administra: generales
 * (sin sede) o propios de una sede, como el 12 de octubre en Guadalajara.
 *
 * Todos los métodos aceptan una sede opcional. Sin sede, solo cuentan los festivos
 * de ley y los días generales de la tabla.
 */
class CalendarioLaboral
{
    /** Día(s) de descanso semanal (0 = domingo … 6 = sábado). */
    public const DIAS_DESCANSO = [CarbonInterface::SUNDAY];

    /** @var array<string, array<string, string>> Festivos por "año-sede" */
    protected static array $cache = [];

    /** @var array<int, array{sede_id: ?int, nombre: string, mes: int, dia: int, anio: ?int}>|null */
    protected static ?array $diasExtra = null;

    /**
     * Festivos de un año para una sede: ['Y-m-d' => 'Descripción'].
     */
    public static function festivos(int $anio, ?int $sedeId = null): array
    {
        $clave = $anio . '-' . ($sedeId ?? 'general');

        if (isset(self::$cache[$clave])) {
            return self::$cache[$clave];
        }

        $festivos = self::festivosDeLey($anio);

        foreach (self::diasExtra() as $dia) {
            // Días de un solo año
            if ($dia['anio'] !== null && (int) $dia['anio'] !== $anio) {
                continue;
            }

            // Días de otra sede
            if ($dia['sede_id'] !== null && (int) $dia['sede_id'] !== $sedeId) {
                continue;
            }

            if (! checkdate((int) $dia['mes'], (int) $dia['dia'], $anio)) {
                continue;
            }

            $festivos[Carbon::create($anio, $dia['mes'], $dia['dia'])->toDateString()] = $dia['nombre'];
        }

        ksort($festivos);

        return self::$cache[$clave] = $festivos;
    }

    /**
     * Festivos entre dos años (inclusive), para enviarlos al calendario del formulario y a la app.
     */
    public static function festivosEntre(int $desde, int $hasta, ?int $sedeId = null): array
    {
        $festivos = [];
        for ($anio = $desde; $anio <= $hasta; $anio++) {
            $festivos += self::festivos($anio, $sedeId);
        }

        return $festivos;
    }

    public static function esFestivo(CarbonInterface $fecha, ?int $sedeId = null): bool
    {
        return array_key_exists($fecha->toDateString(), self::festivos($fecha->year, $sedeId));
    }

    public static function esDescanso(CarbonInterface $fecha): bool
    {
        return in_array($fecha->dayOfWeek, self::DIAS_DESCANSO, true);
    }

    public static function esHabil(CarbonInterface $fecha, ?int $sedeId = null): bool
    {
        return ! self::esDescanso($fecha) && ! self::esFestivo($fecha, $sedeId);
    }

    /**
     * Días hábiles entre dos fechas, incluyendo ambas.
     */
    public static function diasHabiles($inicio, $fin, ?int $sedeId = null): int
    {
        $dia = Carbon::parse($inicio)->startOfDay();
        $fin = Carbon::parse($fin)->startOfDay();

        $dias = 0;
        while ($dia->lte($fin)) {
            if (self::esHabil($dia, $sedeId)) {
                $dias++;
            }
            $dia->addDay();
        }

        return $dias;
    }

    /**
     * Fecha de término al tomar $dias hábiles a partir de $inicio (que debe ser hábil).
     */
    public static function fechaTermino($inicio, int $dias, ?int $sedeId = null): Carbon
    {
        $dia = Carbon::parse($inicio)->startOfDay();
        $contados = self::esHabil($dia, $sedeId) ? 1 : 0;

        while ($contados < $dias) {
            $dia->addDay();
            if (self::esHabil($dia, $sedeId)) {
                $contados++;
            }
        }

        return $dia;
    }

    /**
     * Primer día hábil después de $fecha.
     */
    public static function siguienteHabil($fecha, ?int $sedeId = null): Carbon
    {
        $dia = Carbon::parse($fecha)->startOfDay()->addDay();
        while (! self::esHabil($dia, $sedeId)) {
            $dia->addDay();
        }

        return $dia;
    }

    /**
     * Para pruebas: usa esta lista de días extra en lugar de leer la base de datos.
     * Con null, vuelve a leer la base.
     */
    public static function usarDiasExtra(?array $dias): void
    {
        self::$diasExtra = $dias;
        self::$cache = [];
    }

    /**
     * Olvida lo calculado, para que un cambio en `dias_inhabiles` se tome en cuenta de inmediato.
     */
    public static function limpiarCache(): void
    {
        self::$diasExtra = null;
        self::$cache = [];
    }

    // ─── Internos ─────────────────────────────────────────────────────────────

    /**
     * Festivos de ley y de la empresa, iguales para todas las sedes.
     */
    protected static function festivosDeLey(int $anio): array
    {
        $pascua = self::domingoDePascua($anio);

        $festivos = [
            Carbon::create($anio, 1, 1)->toDateString()                                  => 'Año Nuevo',
            Carbon::create($anio, 2, 1)->nthOfMonth(1, Carbon::MONDAY)->toDateString()   => 'Día de la Constitución',
            Carbon::create($anio, 3, 1)->nthOfMonth(3, Carbon::MONDAY)->toDateString()   => 'Natalicio de Benito Juárez',
            $pascua->copy()->subDays(2)->toDateString()                                  => 'Viernes Santo',
            $pascua->copy()->subDays(1)->toDateString()                                  => 'Sábado Santo',
            Carbon::create($anio, 5, 1)->toDateString()                                  => 'Día del Trabajo',
            Carbon::create($anio, 9, 16)->toDateString()                                 => 'Día de la Independencia',
            Carbon::create($anio, 11, 1)->nthOfMonth(3, Carbon::MONDAY)->toDateString() => 'Día de la Revolución',
            Carbon::create($anio, 12, 24)->toDateString()                                => 'Nochebuena',
            Carbon::create($anio, 12, 25)->toDateString()                                => 'Navidad',
            Carbon::create($anio, 12, 31)->toDateString()                                => 'Fin de año',
        ];

        // Transmisión del Poder Ejecutivo Federal: 1 de octubre cada seis años (2024, 2030…)
        if ($anio >= 2024 && ($anio - 2024) % 6 === 0) {
            $festivos[Carbon::create($anio, 10, 1)->toDateString()] = 'Transmisión del Poder Ejecutivo';
        }

        return $festivos;
    }

    /**
     * Días de la tabla `dias_inhabiles`. Se leen una sola vez por petición.
     * En pruebas unitarias (sin base de datos) devuelve una lista vacía.
     */
    protected static function diasExtra(): array
    {
        if (self::$diasExtra !== null) {
            return self::$diasExtra;
        }

        if (! function_exists('app') || ! app()->bound('db')) {
            return self::$diasExtra = [];
        }

        return self::$diasExtra = DiaInhabil::query()
            ->get(['sede_id', 'nombre', 'mes', 'dia', 'anio'])
            ->map(fn (DiaInhabil $d) => $d->only(['sede_id', 'nombre', 'mes', 'dia', 'anio']))
            ->all();
    }

    /**
     * Domingo de Pascua (algoritmo gregoriano anónimo; no requiere ext-calendar).
     */
    protected static function domingoDePascua(int $anio): Carbon
    {
        $a = $anio % 19;
        $b = intdiv($anio, 100);
        $c = $anio % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $mes = intdiv($h + $l - 7 * $m + 114, 31);
        $dia = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::create($anio, $mes, $dia)->startOfDay();
    }
}