<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Exports\VacacionesExport;
use App\Mail\ExportVacacionesMail;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use App\Models\SolicitudVacacion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportWeekVacacionesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userEmail;

    public function __construct($userEmail)
    {
        $this->userEmail = $userEmail;
    }

    public function handle()
    {
        // Semana nominal: miércoles a martes
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::WEDNESDAY);
        $endOfWeek   = $startOfWeek->copy()->addDays(6)->endOfDay();

        $vacaciones = SolicitudVacacion::with(['empleado', 'departamento'])
            ->whereBetween('fecha_inicio', [$startOfWeek, $endOfWeek])
            ->get();

        $fileName = "vacaciones_semana_" . now()->format('Y-m-d') . "_" . now()->timestamp . ".xlsx";
        $filePath = "exports/{$fileName}";

        Excel::store(new VacacionesExport($vacaciones), $filePath, 'public');

        $url = asset("storage/{$filePath}");
        Mail::to($this->userEmail)->send(new ExportVacacionesMail($filePath, "Exportación Semana Nominal de Vacaciones"));
    }
}