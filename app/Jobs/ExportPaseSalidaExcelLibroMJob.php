<?php

namespace App\Jobs;

use App\Exports\PermissionsExport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Foundation\Queue\Queueable;
use App\Mail\PermisosExcelLMExportadosMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportPaseSalidaExcelLibroMJob implements ShouldQueue
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
        // Construir la consulta para obtener permisos filtrados
        $query = \App\Models\Permission::with(['user.supervisor', 'department', 'approver'])->orderBy('date');

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('date', [
                Carbon::parse($this->startDate)->toDateString(),
                Carbon::parse($this->endDate)->toDateString(),
            ]);
        }

        $permissions = $query->get();

        // Generar el archivo Excel
        $fileName = "permissions_filtered_" . now()->timestamp . ".xlsx";
        $filePath = "exports/{$fileName}";
        $subtitle = PermissionsExport::filtersSubtitle($this->search, $this->startDate, $this->endDate);
        Excel::store(new PermissionsExport($permissions, $subtitle), $filePath, 'public');

        // Enviar el correo con el enlace al archivo
        $url = asset("storage/{$filePath}");
        Mail::to($this->userEmail)->send(new PermisosExcelLMExportadosMail($url));
    }
}
