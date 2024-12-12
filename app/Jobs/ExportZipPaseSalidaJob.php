<?php

namespace App\Jobs;

use PDF;
use ZipArchive;
use App\Models\Permission;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Mail\PermisosZipExportadosMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\EmployeePermission; // Modelo correcto

class ExportZipPaseSalidaJob implements ShouldQueue
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
        // Query para obtener datos de `employee_permissions`
        $query = Permission::with('user', 'department') // Relaciones necesarias
            ->whereIn('status', ['aprobado', 'rechazado', 'pendiente']); // Filtrar por status

        // Filtro por nombre
        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        // Filtro por rango de fechas
        if ($this->startDate && $this->endDate) {
            $startDate = Carbon::parse($this->startDate)->startOfDay();
            $endDate = Carbon::parse($this->endDate)->endOfDay();
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $permissions = $query->get();

        // Crear archivo ZIP
        $zipFileName = "employee_permissions_zip_{$this->userId}_" . now()->timestamp . ".zip";
        $zipFilePath = storage_path("app/exports/{$zipFileName}");

        $zip = new ZipArchive();

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === true) {
            foreach ($permissions as $permission) {
                $pdf = PDF::loadView('permissions.pdf', ['permission' => $permission], [], [
                    'enable-local-file-access' => true, // Permitir acceso a archivos locales
                ]);

                $pdfOutput = $pdf->output();

                $zip->addFromString("permission_{$permission->id}.pdf", $pdfOutput);
            }

            $zip->close();
        } else {
            throw new \Exception("No se pudo crear el archivo ZIP en: {$zipFilePath}");
        }

        // Subir el ZIP a almacenamiento público para descarga
        $storagePath = "exports/{$zipFileName}";
        Storage::disk('public')->put($storagePath, file_get_contents($zipFilePath));

        // Generar enlace de descarga
        $downloadLink = url("storage/{$storagePath}");

        // Enviar correo con el enlace de descarga
        Mail::to($this->userEmail)->send(new PermisosZipExportadosMail($downloadLink));

        // Eliminar el archivo temporal del servidor
        if (file_exists($zipFilePath)) {
            unlink($zipFilePath);
        }
    }
}
