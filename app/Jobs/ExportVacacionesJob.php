<?php

namespace App\Jobs;

use App\Exports\VacacionesExport;
use App\Mail\ExportVacacionesMail;
use App\Mail\VacacionesExportadasMail;
use App\Models\SolicitudVacacion;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportVacacionesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $search;
    protected $startDate;
    protected $endDate;
    protected $userId;
    protected $userEmail;

    public function __construct($search, $startDate, $endDate, $userId, $userEmail)
    {
        $this->search = $search;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->userId = $userId;
        $this->userEmail = $userEmail;
    }

    public function handle()
    {
        

        $query = SolicitudVacacion::with('empleado', 'departamento');

        if ($this->search) {
            $query->whereHas('empleado', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        if ($this->startDate && $this->endDate) {
            $startDate = Carbon::parse($this->startDate)->startOfDay();
            $endDate = Carbon::parse($this->endDate)->endOfDay();
            $query->whereBetween('fecha_inicio', [$startDate, $endDate]);
        }

        $solicitudes = $query->get();

        $fileName = "vacaciones_{$this->userId}_" . now()->timestamp . ".xlsx";
        $filePath = "exports/{$fileName}";
        Excel::store(new VacacionesExport($solicitudes), $filePath, 'public');

        Mail::to($this->userEmail)->send(new ExportVacacionesMail($fileName));

        Storage::delete($filePath); // Opcional: Elimina el archivo después del envío.
        
    }
}
