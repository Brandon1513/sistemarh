<?php

namespace App\Jobs;

use PDF;
use ZipArchive;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use App\Models\SolicitudPermiso;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Mail\PermisosZipExportados;
use App\Mail\PermisosZipExportadosMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportZipPermisosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $search;
    protected $startDate;
    protected $endDate;
    protected $userId;
    protected $userEmail;

    /**
     * Create a new job instance.
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
        $query = SolicitudPermiso::with('empleado', 'departamento')
            ->whereIn('estado', ['aprobado', 'rechazado', 'pendiente']);

        // Aplicar filtro por nombre
        if ($this->search) {
            $query->whereHas('empleado', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        // Aplicar filtro por rango de fechas
        if ($this->startDate && $this->endDate) {
            $startDate = Carbon::parse($this->startDate)->startOfDay();
            $endDate = Carbon::parse($this->endDate)->endOfDay();
            $query->whereBetween('fecha_inicio', [$startDate, $endDate]);
        }

        $permisos = $query->get();

        // Crear el archivo ZIP
        $zipFileName = "permisos_zip_{$this->userId}_" . now()->timestamp . ".zip";
        $zipFilePath = storage_path("app/exports/{$zipFileName}");

        $zip = new ZipArchive();
        \Log::info("Creando el archivo ZIP en: {$zipFilePath}");
        \Log::info("Cantidad de permisos: " . count($permisos));
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === true) {
            foreach ($permisos as $permiso) {
                $pdf = PDF::loadView('solicitudes_permisos.pdf', ['permiso' => $permiso], [], [
                    'enable-local-file-access' => true, // Permitir acceso a archivos locales
                ]);
        
                $pdfOutput = $pdf->output();
        
                $zip->addFromString("permiso_{$permiso->id}.pdf", $pdfOutput);
            }
        
            $zip->close(); // Importante cerrar el ZIP aquí
        } else {
            \Log::error("No se pudo abrir el archivo ZIP");
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