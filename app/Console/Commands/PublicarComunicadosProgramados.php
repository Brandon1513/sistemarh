<?php

namespace App\Console\Commands;

use App\Models\Comunicado;
use App\Services\ComunicadosService;
use Illuminate\Console\Command;

class PublicarComunicadosProgramados extends Command
{
    protected $signature = 'comunicados:publicar-programados';

    protected $description = 'Publica los comunicados programados cuya hora ya llegó';

    public function handle(ComunicadosService $comunicados): int
    {
        $pendientes = Comunicado::where('estado', 'programado')
            ->where('publicar_en', '<=', now())
            ->get();

        foreach ($pendientes as $comunicado) {
            $comunicados->publicar($comunicado);
            $this->info("Publicado: {$comunicado->titulo}");
        }

        return self::SUCCESS;
    }
}