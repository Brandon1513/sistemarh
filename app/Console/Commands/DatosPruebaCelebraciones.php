<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DatosPruebaCelebraciones extends Command
{
    protected $signature = 'celebraciones:datos-prueba';

    protected $description = 'SOLO LOCAL: fechas de nacimiento y turnos falsos, con algunos cumpleaños y aniversarios hoy';

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('Este comando no se puede usar en producción.');

            return self::FAILURE;
        }

        $hoy = now();
        $usuarios = User::all();

        foreach ($usuarios as $i => $usuario) {
            $usuario->forceFill([
                // Fecha de nacimiento al azar entre 20 y 55 años atrás
                'fecha_nacimiento' => Carbon::create(rand(1971, 2005), rand(1, 12), rand(1, 28)),
                'turno'            => $i % 4 === 0 ? 'nocturno' : 'diurno',
            ])->save();
        }

        // Tres que cumplen años hoy, y dos con aniversario hoy (para probar)
        $usuarios->shuffle()->take(3)->each(fn ($u) => $u->forceFill([
            'fecha_nacimiento' => Carbon::create(rand(1975, 2000), $hoy->month, $hoy->day),
        ])->save());

        $usuarios->shuffle()->take(2)->each(fn ($u) => $u->forceFill([
            'fecha_ingreso' => Carbon::create($hoy->year - rand(1, 10), $hoy->month, $hoy->day),
        ])->save());

        $this->info("Listo: {$usuarios->count()} empleados con datos de prueba.");
        $this->line('Cumplen años hoy: ' . User::cumpleanosEl($hoy)->pluck('name')->implode(', '));
        $this->line('Aniversario hoy:  ' . User::aniversarioEl($hoy)->get()->map(fn ($u) => "{$u->name} ({$u->aniosEnLaEmpresa($hoy)} años)")->implode(', '));

        return self::SUCCESS;
    }
}