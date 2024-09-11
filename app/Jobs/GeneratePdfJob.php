<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PDF;
use ZipArchive;
use Storage;

class GeneratePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $permissions;

    public function __construct($permissions)
    {
        $this->permissions = $permissions;
    }

    public function handle()
{
    try {
        \Log::info('Iniciando generación de ZIP');
        $zip = new ZipArchive();
        $fileName = 'permissions_' . now()->format('Y-m-d_H:i:s') . '.zip';
        $zipPath = storage_path('app/public/zips/' . $fileName);

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            \Log::info('ZIP creado correctamente en: ' . $zipPath);
            foreach ($this->permissions as $permission) {
                // Generar el PDF
                $pdf = PDF::loadView('permissions.pdf', compact('permission'));
                $pdfPath = storage_path('app/public/pdf/' . 'permission_' . $permission->id . '.pdf');

                // Almacenar el PDF
                \Log::info('Guardando PDF en: ' . $pdfPath);
                Storage::put('public/pdf/permission_' . $permission->id . '.pdf', $pdf->output());

                // Agregar al ZIP
                \Log::info('Agregando PDF al ZIP: ' . $pdfPath);
                $zip->addFile($pdfPath, 'permission_' . $permission->id . '.pdf');
            }
            $zip->close();
            \Log::info('ZIP cerrado correctamente');
        } else {
            \Log::error('No se pudo abrir el archivo ZIP en: ' . $zipPath);
        }
    } catch (\Exception $e) {
        \Log::error('Error en GeneratePdfJob: ' . $e->getMessage());
        throw $e;
    }
}


}





