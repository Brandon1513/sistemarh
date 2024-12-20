<?php

namespace App\Jobs;

use App\Models\SolicitudVacacion;
use App\Exports\VacacionesExport;
use App\Mail\ExportVacacionesMail;
use App\Mail\ExportWeekVacacionesMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportWeekVacacionesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $startDate;
    protected $endDate;
    protected $userId;
    protected $userEmail;

    /**
     * Create a new job instance.
     */
    public function __construct($startDate, $endDate, $userId, $userEmail)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->userId = $userId;
        $this->userEmail = $userEmail;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
       

        // Filtrar las solicitudes de vacaciones dentro del rango
        $vacaciones = SolicitudVacacion::whereBetween('fecha_inicio', [$this->startDate, $this->endDate])
            ->with('empleado', 'departamento') // Relaciones necesarias
            ->get();

        // Generar el archivo Excel
        $fileName = "vacaciones_semanales_{$this->userId}_" . now()->timestamp . ".xlsx";
        $filePath = "exports/{$fileName}";
        Excel::store(new VacacionesExport($vacaciones), $filePath, 'public');

        // Enviar correo con el enlace al archivo
        Mail::to($this->userEmail)->send(new ExportWeekVacacionesMail($fileName));
    }
}
