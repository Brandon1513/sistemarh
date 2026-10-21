<?php

namespace App\Jobs;

use App\Exports\VacacionesExport;
use App\Mail\ExportVacacionesMail;
use App\Models\SolicitudVacacion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportLibroMayorVacacionesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $search;
    protected $startDate;
    protected $endDate;
    protected $userEmail;

    /**
     * Create a new job instance.
     *
     * @param string|null $search
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string $userEmail
     */
    public function __construct($search, $startDate, $endDate, $userEmail)
    {
        $this->search = $search;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->userEmail = $userEmail;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Construir la consulta para obtener las solicitudes de vacaciones filtradas
        $query = SolicitudVacacion::with(['empleado.supervisor', 'departamento'])->orderBy('fecha_inicio');

        if ($this->search) {
            $query->whereHas('empleado', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('fecha_inicio', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        $vacaciones = $query->get();

        // Generar el archivo Excel
        $fileName = "libro_mayor_vacaciones_" . now()->timestamp . ".xlsx";
        $filePath = "exports/{$fileName}";
        $subtitle = VacacionesExport::filtersSubtitle($this->search, $this->startDate, $this->endDate);
        Excel::store(new VacacionesExport($vacaciones, $subtitle), $filePath, 'public');

        // El correo arma el enlace de descarga a partir de la ruta relativa
        Mail::to($this->userEmail)->send(new ExportVacacionesMail($filePath, 'Libro Mayor de Vacaciones'));
        }
}
