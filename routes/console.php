<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule as FacadeSchedule;
use App\Http\Controllers\PeriodoVacacionController;
use Illuminate\Support\Facades\Schedule;

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

// Revisa cada minuto si hay comunicados programados cuya hora ya llegó
Schedule::command('comunicados:publicar-programados')->everyMinute()->withoutOverlapping();

// Manda los correos pendientes de la cola. Termina cuando ya no hay nada o a los 40 segundos,
// para que la siguiente vuelta del despertador no se encime.
Schedule::command('queue:work --stop-when-empty --max-time=40 --tries=3')
    ->everyMinute()
    ->withoutOverlapping();
