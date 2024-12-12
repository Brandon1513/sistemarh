<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use App\Exports\PermisosExport;
use App\Models\SolicitudPermiso;
use App\Mail\PermisosExportadosMail;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportPermisosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $search;
    protected $startDate;
    protected $endDate;
    protected $userId;
    protected $userEmail;

    /**
     * Create a new job instance.
     *
     * @param string|null $search
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int $userId
     * @param string $userEmail
     */
    public function __construct($search, $startDate, $endDate, $userId, $userEmail)
    {
        $this->search = $search;
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
    $query = SolicitudPermiso::with('empleado', 'departamento');

    // Aplicar filtro por nombre
    if ($this->search) {
        $query->whereHas('empleado', function ($q) {
            $q->where('name', 'like', "%{$this->search}%");
        });
    }

    // Aplicar filtro por fechas
    if ($this->startDate && $this->endDate) {
        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();
        $query->whereBetween('fecha_inicio', [$startDate, $endDate]);
    }

    // Obtener los permisos filtrados
    $solicitudes = $query->get();

    // Generar el archivo Excel
    $fileName = "controlausencia_{$this->userId}_" . now()->timestamp . ".xlsx";
    $filePath = "exports/{$fileName}";
    Excel::store(new PermisosExport($solicitudes), $filePath, 'public');


    Mail::to($this->userEmail)->send(new PermisosExportadosMail($fileName));
}

}
