<?php

namespace App\Jobs;

use App\Exports\VacacionesExport;
use App\Models\SolicitudVacacion;
use App\Mail\ExportVacacionesMail;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Foundation\Queue\Queueable;
use App\Mail\PermisosExcelLMExportadosMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportLibroMayorVacacionesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $search;
    protected $date;
    protected $userEmail;

    /**
     * Create a new job instance.
     *
     * @param string|null $search
     * @param string|null $date
     * @param string $userEmail
     */
    public function __construct($search, $date, $userEmail)
    {
        $this->search = $search;
        $this->date = $date;
        $this->userEmail = $userEmail;
    }

    /**
     * Execute the job.
     */
    public function handle()
{
    $query = SolicitudVacacion::with(['empleado', 'departamento']);
 
    if ($this->search) {
        $query->whereHas('empleado', function ($q) {
            $q->where('name', 'like', "%{$this->search}%");
        });
    }
 
    if ($this->date) {
        $query->whereDate('fecha_inicio', $this->date);
    }
 
    $vacaciones = $query->get();
 
    $fileName = "vacaciones_libro_mayor_" . now()->timestamp . ".xlsx";
    $filePath = "exports/{$fileName}";
 
    Excel::store(new VacacionesExport($vacaciones), $filePath, 'public');
 
    Mail::to($this->userEmail)->send(new ExportVacacionesMail($filePath, 'Libro Mayor de Vacaciones'));
}

}