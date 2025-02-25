<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PeriodoVacacionController;

class ActivarPeriodosVacaciones extends Command
{
    protected $signature = 'periodos:activar';
    protected $description = 'Activa los periodos de vacaciones automáticamente cuando los empleados cumplen aniversario';

    public function handle()
    {
        $controller = new PeriodoVacacionController();
        $controller->activarPeriodosAutomáticamente();

        $this->info('Periodos de vacaciones revisados y activados correctamente.');
    }
}

