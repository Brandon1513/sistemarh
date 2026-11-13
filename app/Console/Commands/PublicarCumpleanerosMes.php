<?php

namespace App\Console\Commands;

use App\Services\Celebraciones\CelebracionesService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PublicarCumpleanerosMes extends Command
{
    protected $signature = 'celebraciones:lista-mes {--mes= : AAAA-MM, para probar con otro mes} {--forzar : Volver a generarla aunque ya exista}';

    protected $description = 'Publica la lista de cumpleañeros del mes';

    public function handle(CelebracionesService $celebraciones): int
    {
        $mes = $this->option('mes') ? Carbon::parse($this->option('mes') . '-01') : today();

        $comunicado = $celebraciones->publicarListaMes($mes, (bool) $this->option('forzar'));

        $this->line($comunicado
            ? "🎂 Publicada: {$comunicado->titulo}"
            : 'No se publicó (ya existía, o nadie cumple años ese mes). Usa --forzar para regenerarla.');

        return self::SUCCESS;
    }
}