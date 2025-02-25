<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\PeriodoVacacionController;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('periodos:activar', function () {
    $controller = new PeriodoVacacionController();
    $controller->activarPeriodosAutomáticamente();
    $this->info('Periodos de vacaciones revisados y activados correctamente.');
})->describe('Activa automáticamente los periodos de vacaciones cuando un empleado cumple su aniversario');
