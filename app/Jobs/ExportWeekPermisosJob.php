<?php

namespace App\Jobs;

use App\Exports\PermisosExport;
use App\Mail\ExportWeekPermisosMail;
use App\Models\SolicitudPermiso;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class ExportWeekPermisosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $userEmail;

    /**
     * Create a new job instance.
     *
     * @param int $userId
     * @param string $userEmail
     */
    public function __construct($userId, $userEmail)
    {
        $this->userId = $userId;
        $this->userEmail = $userEmail;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Definir rango de semana nominal (miércoles a martes)
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::WEDNESDAY);
        $endOfWeek = Carbon::now()->addWeek()->startOfWeek(Carbon::TUESDAY)->endOfDay();

        // Obtener permisos de la semana nominal
        $solicitudes = SolicitudPermiso::with('empleado.supervisor', 'departamento')
            ->whereBetween('fecha_inicio', [$startOfWeek, $endOfWeek])
            ->orderBy('fecha_inicio')
            ->get();

        // Generar archivo Excel
        $fileName = "permisos_semana_nominal_{$this->userId}_" . now()->timestamp . ".xlsx";
        $filePath = "exports/{$fileName}";
        $subtitle = PermisosExport::weekSubtitle($startOfWeek, $endOfWeek);
        Excel::store(new PermisosExport($solicitudes, $subtitle), $filePath, 'public');

        // Enviar correo con el enlace de descarga
        Mail::to($this->userEmail)->send(new ExportWeekPermisosMail($fileName));
    }
}
