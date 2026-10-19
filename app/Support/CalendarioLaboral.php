<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Calendario laboral de Dasavena: qué días cuentan como hábiles para vacaciones.
 *
 * Día de descanso: domingo.
 * Festivos: los de ley (LFT art. 74) más los que otorga la empresa. Los que se
 * mueven cada año (lunes de febrero, marzo y noviembre, Semana Santa) se calculan.
 */
class CalendarioLaboral
{
    /** Día(s) de descanso semanal (0 = domingo … 6 = sábado). */
    public const DIAS_DESCANSO = [CarbonInterface::SUNDAY];

    /** @var array<int, array<string, string>> */
    protected static array $cache = [];

    /**
     * Festivos de un año: ['Y-m-d' => 'Descripción'].
     */
    public static function festivos(int $anio): array
    {
        if (isset(self::$cache[$anio])) {
            return self::$cache[$anio];
        }

        $pascua = self::domingoDePascua($anio);

        $festivos = [
            Carbon::create($anio, 1, 1)->toDateString()                                  => 'Año Nuevo',
            Carbon::create($anio, 2, 1)->nthOfMonth(1, Carbon::MONDAY)->toDateString()   => 'Día de la Constitución',
            Carbon::create($anio, 3, 1)->nthOfMonth(3, Carbon::MONDAY)->toDateString()   => 'Natalicio de Benito Juárez',
            $pascua->copy()->subDays(2)->toDateString()                                  => 'Viernes Santo',
            $pascua->copy()->subDays(1)->toDateString()                                  => 'Sábado Santo',
            Carbon::create($anio, 5, 1)->toDateString()                                  => 'Día del Trabajo',
            Carbon::create($anio, 9, 16)->toDateString()                                 => 'Día de la Independencia',
            Carbon::create($anio, 10, 12)->toDateString()                                => 'Día de la Raza',
            Carbon::create($anio, 11, 1)->nthOfMonth(3, Carbon::MONDAY)->toDateString() => 'Día de la Revolución',
            Carbon::create($anio, 12, 24)->toDateString()                                => 'Nochebuena',
            Carbon::create($anio, 12, 25)->toDateString()                                => 'Navidad',
            Carbon::create($anio, 12, 31)->toDateString()                                => 'Fin de año',
        ];

        // Transmisión del Poder Ejecutivo Federal: 1 de octubre cada seis años (2024, 2030…)
        if ($anio >= 2024 && ($anio - 2024) % 6 === 0) {
            $festivos[Carbon::create($anio, 10, 1)->toDateString()] = 'Transmisión del Poder Ejecutivo';
        }

        ksort($festivos);

        return self::$cache[$anio] = $festivos;
    }

    /**
     * Festivos entre dos años (inclusive), para enviarlos al calendario del formulario.
     */
    public static function festivosEntre(int $desde, int $hasta): array
    {
        $festivos = [];
        for ($anio = $desde; $anio <= $hasta; $anio++) {
            $festivos += self::festivos($anio);
        }

        return $festivos;
    }

    public static function esFestivo(CarbonInterface $fecha): bool
    {
        return array_key_exists($fecha->toDateString(), self::festivos($fecha->year));
    }

    public static function esDescanso(CarbonInterface $fecha): bool
    {
        return in_array($fecha->dayOfWeek, self::DIAS_DESCANSO, true);
    }

    public static function esHabil(CarbonInterface $fecha): bool
    {
        return ! self::esDescanso($fecha) && ! self::esFestivo($fecha);
    }

    /**
     * Días hábiles entre dos fechas, incluyendo ambas.
     */
    public static function diasHabiles($inicio, $fin): int
    {
        $dia = Carbon::parse($inicio)->startOfDay();
        $fin = Carbon::parse($fin)->startOfDay();

        $dias = 0;
        while ($dia->lte($fin)) {
            if (self::esHabil($dia)) {
                $dias++;
            }
            $dia->addDay();
        }

        return $dias;
    }

    /**
     * Fecha de término al tomar $dias hábiles a partir de $inicio (que debe ser hábil).
     */
    public static function fechaTermino($inicio, int $dias): Carbon
    {
        $dia = Carbon::parse($inicio)->startOfDay();
        $contados = self::esHabil($dia) ? 1 : 0;

        while ($contados < $dias) {
            $dia->addDay();
            if (self::esHabil($dia)) {
                $contados++;
            }
        }

        return $dia;
    }

    /**
     * Primer día hábil después de $fecha.
     */
    public static function siguienteHabil($fecha): Carbon
    {
        $dia = Carbon::parse($fecha)->startOfDay()->addDay();
        while (! self::esHabil($dia)) {
            $dia->addDay();
        }

        return $dia;
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
