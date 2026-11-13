<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Celebraciones\CelebracionesService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PublicarCelebracionesDia extends Command
{
    protected $signature = 'celebraciones:publicar-dia {--fecha= : AAAA-MM-DD, para probar con otro día}';

    protected $description = 'Archiva las celebraciones de ayer y publica los cumpleaños y aniversarios de hoy';

    public function handle(CelebracionesService $celebraciones): int
    {
        $dia = $this->option('fecha') ? Carbon::parse($this->option('fecha'))->startOfDay() : today();

        $archivadas = $celebraciones->archivarAnteriores($dia);
        $this->line("Archivadas del día anterior: {$archivadas}");

        foreach (User::cumpleanosEl($dia)->get() as $persona) {
            $hecho = $celebraciones->publicarCumpleanos($persona, $dia);
            $this->line(($hecho ? '🎂 Publicado: ' : '— Ya estaba: ') . $persona->name);
        }

        foreach (User::aniversarioEl($dia)->get() as $persona) {
            $hecho = $celebraciones->publicarAniversario($persona, $dia);
            $this->line(($hecho ? '🎉 Publicado: ' : '— Ya estaba: ') . "{$persona->name} ({$persona->aniosEnLaEmpresa($dia)} años)");
        }

        return self::SUCCESS;
    }
}