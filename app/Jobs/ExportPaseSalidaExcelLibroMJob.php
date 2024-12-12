<?php

namespace App\Jobs;

use App\Exports\PermissionsExport;
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
        // Construir la consulta para obtener permisos filtrados
        $query = \App\Models\Permission::with(['user', 'department']);

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        if ($this->date) {
            $query->whereDate('date', $this->date);
        }

        $permissions = $query->get();

        // Generar el archivo Excel
        $fileName = "permissions_filtered_" . now()->timestamp . ".xlsx";
        $filePath = "exports/{$fileName}";
        Excel::store(new PermissionsExport($permissions), $filePath, 'public');

        // Enviar el correo con el enlace al archivo
        $url = asset("storage/{$filePath}");
        Mail::to($this->userEmail)->send(new PermisosExcelLMExportadosMail($url));
    }
}
