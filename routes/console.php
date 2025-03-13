<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule as FacadeSchedule;
use App\Http\Controllers\PeriodoVacacionController;

// Comando que muestra una frase inspiradora cada hora
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Comando para activar los períodos de vacaciones automáticamente
Artisan::command('periodos:activar', function () {
    $controller = new PeriodoVacacionController();
    $controller->activarPeriodosAutomáticamente();
    $this->info('Periodos de vacaciones revisados y activados correctamente.');
})->describe('Activa automáticamente los periodos de vacaciones cuando un empleado cumple su aniversario')
  ->daily(); // 🔹 Se ejecutará una vez al día  

// Para pruebas, puedes cambiarlo a cada minuto:
//FacadeSchedule::command('periodos:activar')->everyMinute();
