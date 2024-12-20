<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ExportVacacionesJob;
use App\Jobs\ExportWeekVacacionesJob;
use App\Jobs\ExportZIPVacacionesJob;
use Illuminate\Support\Carbon;

class ExportVacacionesController extends Controller
{
    public function export(Request $request)
    {
        \Log::info('Exportación de vacaciones iniciada.', $request->all());

        // Extraer parámetros de la solicitud
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        
        \Log::info('Preparando para despachar el job ExportVacacionesJob con parámetros:', compact('search', 'startDate', 'endDate'));

        ExportVacacionesJob::dispatch($search, $startDate, $endDate, auth()->id(), auth()->user()->email);

        \Log::info('Job ExportVacacionesJob despachado correctamente.');

        return redirect()->back()->with('success', 'La exportación de vacaciones está en proceso. Recibirás un correo cuando esté lista.');
    }
    public function exportWeek(Request $request)
    {
    \Log::info('Exportación semanal iniciada.');

    // Calcular las fechas de la semana nominal (miércoles a martes)
    $currentDate = now();
    $startOfWeek = $currentDate->copy()->startOfWeek(Carbon::WEDNESDAY); // Inicio de la semana nominal
    $endOfWeek = $startOfWeek->copy()->addDays(6); // Fin de la semana nominal

    \Log::info("Rango de la semana nominal: {$startOfWeek} - {$endOfWeek}");

    // Despachar el Job para exportar las vacaciones de la semana nominal
    ExportWeekVacacionesJob::dispatch($startOfWeek, $endOfWeek, auth()->id(), auth()->user()->email)
        ->onQueue('default');

    return redirect()->back()->with('success', 'La exportación semanal está en proceso. Recibirás un correo cuando esté lista.');
    }
    public function exportZip(Request $request)
    {
        $search = $request->input('search');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $userId = auth()->id();
    $userEmail = auth()->user()->email;

    // Llamar al Job
    ExportZIPVacacionesJob::dispatch($search, $startDate, $endDate, $userId, $userEmail);

    return redirect()->back()->with('success', 'La exportación en ZIP se está procesando. Recibirás un correo cuando esté lista.');
    }
}
