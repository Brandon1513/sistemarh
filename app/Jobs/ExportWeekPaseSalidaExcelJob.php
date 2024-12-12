<?php

namespace App\Jobs;

use App\Models\Permission;
use App\Exports\PermissionsExport;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\ExportWeekPaseSalidaMail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExportWeekPaseSalidaExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userEmail;

    /**
     * Create a new job instance.
     *
     * @param string $userEmail
     */
    public function __construct($userEmail)
    {
        $this->userEmail = $userEmail;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Calcular las fechas de la semana nominal actual
        $startOfWeek = now()->startOfWeek()->addDays(3); // Miércoles
        $endOfWeek = $startOfWeek->copy()->addDays(6); // Martes de la próxima semana

        // Filtrar los permisos dentro del rango de fechas
        $permissions = Permission::with(['user', 'department'])
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->get();

        // Generar el archivo Excel
        $fileName = "permissions_week_" . now()->timestamp . ".xlsx";
        $filePath = "exports/{$fileName}";
        Excel::store(new PermissionsExport($permissions), $filePath, 'public');

        // Crear el enlace de descarga
        $downloadUrl = asset("storage/{$filePath}");

        // Enviar el correo con el enlace de descarga
        Mail::to($this->userEmail)->send(new ExportWeekPaseSalidaMail($downloadUrl));
    }
}