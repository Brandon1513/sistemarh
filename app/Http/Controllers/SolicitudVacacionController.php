<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\PeriodoVacacion;
use App\Models\VacationRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SolicitudVacacion;
use Illuminate\Support\Facades\Mail;
use App\Mail\VacationRequestNotification;
use App\Jobs\ExportLibroMayorVacacionesJob;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VacationRHNotification;
use App\Notifications\VacationApprovedNotification;
use App\Notifications\VacationRejectedNotification;
use App\Notifications\VacationSecurityNotification;
use Illuminate\Support\Str;
use App\Support\CalendarioLaboral;
use Illuminate\Support\Facades\DB;


class SolicitudVacacionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
    
        if ($user->hasRole('jefe')) {
            $solicitudes = SolicitudVacacion::whereHas('empleado', function ($query) use ($user) {
                $query->where('supervisor_id', $user->id);
            })->orWhere('empleado_id', $user->id)
              ->with('empleado', 'departamento')
              ->orderBy('created_at', 'desc') // Ordenar por fecha de creación, del más reciente al más antiguo
              ->paginate(10); // Paginación de 10 solicitudes por página
        } else {
            $solicitudes = SolicitudVacacion::where('empleado_id', $user->id)
                ->with('empleado', 'departamento')
                ->orderBy('created_at', 'desc') // Ordenar del más reciente al más antiguo
                ->paginate(10); // Paginación de 10 solicitudes por página
        }
    
        return view('vacaciones.index', compact('solicitudes'));
    }
    

    public function create()
    {
        $user = auth()->user(); // Usuario autenticado
    
        // Obtener solo los periodos activos del usuario autenticado
        $periodos = PeriodoVacacion::where('empleado_id', $user->id)
                                    ->where('activo', 1) // Filtra solo los periodos activos
                                    ->get();
    
        $departments = Department::all(); // Obtiene todos los departamentos
    
        // Verifica que el usuario tenga un departamento asociado
        $departamentoNombre = $user->departamento ? $user->departamento->name : 'Sin departamento';

        // Festivos para bloquear y descontar días en el calendario del formulario
        $festivos = CalendarioLaboral::festivosEntre(now()->year, now()->year + 1);

        return view('vacaciones.create', compact('user', 'departments', 'departamentoNombre', 'periodos', 'festivos'));
    }
public function show($id)
{
    $vacationRequest = SolicitudVacacion::with('empleado', 'departamento')->findOrFail($id);
    return view('vacaciones.show', compact('vacationRequest'));
}



public function store(Request $request)
{
    $user = auth()->user();

    // Validación de los datos
    $request->validate([
        'dias_solicitados' => 'required|integer|min:1',
        'fecha_inicio_vacaciones' => 'required|date|after_or_equal:today',
        'fecha_termino_vacaciones' => 'required|date|after_or_equal:fecha_inicio_vacaciones',
        'periodo_correspondiente' => 'required|integer',
    ], [
        'fecha_inicio_vacaciones.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
        'fecha_termino_vacaciones.after_or_equal' => 'La fecha de término no puede ser anterior a la de inicio.',
    ]);

    $diasSolicitados = (int) $request->dias_solicitados;
    $inicio = Carbon::parse($request->fecha_inicio_vacaciones)->startOfDay();
    $fin    = Carbon::parse($request->fecha_termino_vacaciones)->startOfDay();

    // Las vacaciones deben empezar y terminar en día hábil
    if (!CalendarioLaboral::esHabil($inicio)) {
        return back()->withInput()->withErrors(['fecha_inicio_vacaciones' => 'La fecha de inicio cae en domingo o día festivo.']);
    }
    if (!CalendarioLaboral::esHabil($fin)) {
        return back()->withInput()->withErrors(['fecha_termino_vacaciones' => 'La fecha de término cae en domingo o día festivo.']);
    }

    // Las fechas deben cubrir exactamente los días solicitados (sin domingos ni festivos)
    $diasHabiles = CalendarioLaboral::diasHabiles($inicio, $fin);
    if ($diasHabiles !== $diasSolicitados) {
        return back()->withInput()->withErrors([
            'fecha_termino_vacaciones' => "Del {$inicio->format('d/m/Y')} al {$fin->format('d/m/Y')} hay {$diasHabiles} días hábiles, pero solicitaste {$diasSolicitados}.",
        ]);
    }

    // No puede encimarse con otra solicitud pendiente o aprobada
    $traslape = SolicitudVacacion::where('empleado_id', $user->id)
        ->whereIn('estado', ['pendiente', 'aprobado'])
        ->whereDate('fecha_inicio', '<=', $fin)
        ->whereDate('fecha_fin', '>=', $inicio)
        ->first();
    if ($traslape) {
        return back()->withInput()->withErrors([
            'fecha_inicio_vacaciones' => 'Ya tienes una solicitud ' . $traslape->estado . ' del '
                . Carbon::parse($traslape->fecha_inicio)->format('d/m/Y') . ' al '
                . Carbon::parse($traslape->fecha_fin)->format('d/m/Y') . ' que se cruza con estas fechas.',
        ]);
    }

    $fechaReincorporacion = CalendarioLaboral::siguienteHabil($fin);

    // Descontar los días y crear la solicitud en una transacción, bloqueando el periodo
    // para que dos envíos simultáneos no descuenten dos veces del mismo saldo.
    $vacationRequest = DB::transaction(function () use ($user, $request, $diasSolicitados, $inicio, $fin, $fechaReincorporacion) {
        $periodo = PeriodoVacacion::where('empleado_id', $user->id)
                                   ->where('anio', $request->periodo_correspondiente)
                                   ->where('activo', 1)
                                   ->lockForUpdate()
                                   ->first();

        if (!$periodo) {
            return null;
        }

        if ($periodo->dias_disponibles < $diasSolicitados) {
            return false;
        }

        $periodo->dias_disponibles -= $diasSolicitados;
        $periodo->save();

        return SolicitudVacacion::create([
            'empleado_id' => $user->id,
            'departamento_id' => $user->departamento_id,
            'fecha_solicitud' => now(),
            'dias_corresponden' => $periodo->dias_corresponden,
            'dias_solicitados' => $diasSolicitados,
            'dias_pendientes' => $periodo->dias_disponibles,
            'periodo_correspondiente' => $periodo->anio,
            'fecha_inicio' => $inicio->toDateString(),
            'fecha_fin' => $fin->toDateString(),
            'fecha_reincorporacion' => $fechaReincorporacion->toDateString(),
            'estado' => 'pendiente',
        ]);
    });

    if ($vacationRequest === null) {
        return back()->withInput()->withErrors(['periodo_correspondiente' => 'El periodo seleccionado no existe o no está activo.']);
    }
    if ($vacationRequest === false) {
        return back()->withInput()->withErrors(['dias_solicitados' => 'No tienes suficientes días disponibles para este período.']);
    }

    // Enviar correo al jefe directo
    $supervisor = $user->supervisor; // Asegúrate de que existe una relación 'supervisor' en el modelo User
    if ($supervisor && $supervisor->email) {
        Mail::to($supervisor->email)->send(new VacationRequestNotification($user, $vacationRequest));
    }

    return redirect()->route('vacaciones.index')->with('success', 'Solicitud de vacaciones creada correctamente y notificación enviada al jefe directo.');
}

    

public function approve($id)
{
    if (!auth()->check()) {
        session(['url.intended' => route('vacaciones.aprobar', $id)]);
        return redirect()->route('login');
    }

    $vacationRequest = SolicitudVacacion::findOrFail($id);
    $vacationRequest->estado = 'aprobado';
    $vacationRequest->save();

    $employee = $vacationRequest->empleado;
    $rhEmails = User::role('recursos_humanos')->pluck('email')->toArray();

    // Notificar al empleado
    $employee->notify(new VacationApprovedNotification($vacationRequest));

    // Notificar al departamento de Recursos Humanos
    Notification::route('mail', $rhEmails)->notify(new VacationRHNotification($vacationRequest, 'aprobada'));

    // Notificar al personal de seguridad
    $personalSeguridad = User::role('seguridad')->get(); // Suponiendo que tienes un rol 'seguridad'
    foreach ($personalSeguridad as $persona) {
        $persona->notify(new VacationSecurityNotification($vacationRequest));
    }

    return redirect()->route('vacaciones.index')->with('success', 'Solicitud aprobada y notificaciones enviadas.');
}

public function reject($id)
{
    if (!auth()->check()) {
        session(['url.intended' => route('vacaciones.rechazar', $id)]);
        return redirect()->route('login');
    }

    // Encuentra la solicitud de vacaciones
    $vacationRequest = SolicitudVacacion::findOrFail($id);

    // Si la solicitud ya está rechazada, no hacemos nada
    if ($vacationRequest->estado === 'rechazado') {
        return redirect()->route('vacaciones.index')->with('info', 'La solicitud ya está rechazada.');
    }

    // Devuelve los días solicitados al periodo correspondiente
    $periodo = PeriodoVacacion::where('empleado_id', $vacationRequest->empleado_id)
                            ->where('anio', $vacationRequest->periodo_correspondiente)
                            ->first();

    if ($periodo) {
        $periodo->dias_disponibles += $vacationRequest->dias_solicitados;
        $periodo->save();
    }

    // Actualiza el estado de la solicitud a 'rechazado'
    $vacationRequest->estado = 'rechazado';
    $vacationRequest->save();

    // Notificar al empleado
    $employee = $vacationRequest->empleado;
    $employee->notify(new VacationRejectedNotification($vacationRequest));

    // Notificar al departamento de Recursos Humanos
    $rhEmails = User::role('recursos_humanos')->pluck('email')->toArray();
    Notification::route('mail', $rhEmails)->notify(new VacationRHNotification($vacationRequest, 'rechazada'));

    return redirect()->route('vacaciones.index')->with('success', 'Solicitud rechazada y días devueltos correctamente.');
}


public function indexRH(Request $request)
{
    $query = SolicitudVacacion::with(['empleado', 'departamento'])
        ->whereIn('estado', ['aprobado', 'rechazado', 'pendiente']);

    // Filtrar por nombre de empleado si se proporciona
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->whereHas('empleado', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });
    }

    // Filtrar por rango de fechas si se proporciona
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        $query->whereBetween('fecha_inicio', [$startDate, $endDate]);
    }

    // Si no se proporciona ni nombre ni rango de fechas, aplicar el filtro de la semana nominal
    if (!$request->filled('search') && !$request->filled('start_date') && !$request->filled('end_date')) {
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::WEDNESDAY); // Inicio de la semana (miércoles)
        $endOfWeek = $startOfWeek->copy()->addDays(6)->endOfDay();    // Fin de la semana (martes)
        $query->whereBetween('fecha_inicio', [$startOfWeek, $endOfWeek]);
    }

    $vacaciones = $query->get();

    return view('solicitudes_vacaciones.index', ['vacations' => $vacaciones]);
}
public function showRH($id)
{
    $vacation = SolicitudVacacion::with(['empleado', 'departamento'])->findOrFail($id);
    return view('solicitudes_vacaciones.show', compact('vacation'));
}

public function downloadPDF($id)
{
    $vacation = SolicitudVacacion::with(['empleado', 'departamento'])->findOrFail($id);
    
    $pdf = Pdf::loadView('solicitudes_vacaciones.pdf', compact('vacation'));
    return $pdf->download('solicitud_vacaciones_' . $vacation->id . '.pdf');
}
public function export(Request $request)
{
    ExportLibroMayorVacacionesJob::dispatch(
        $request->input('search'),
        $request->input('start_date'),
        $request->input('end_date'),
        auth()->user()->email
    );

    return back()->with('success', 'El archivo está siendo procesado y será enviado por correo.');
}
    public function destroy($id)
    {
        // 1. Buscar la solicitud
        $vac = SolicitudVacacion::findOrFail($id);

        // 2. Only pending
        if (Str::lower($vac->estado) !== 'pendiente') {
            return redirect()
                ->route('vacaciones.index')
                ->with('error', 'Solo puedes eliminar solicitudes pendientes.');
        }

        // 3. Devolver los días al periodo
        $periodo = PeriodoVacacion::where('empleado_id', $vac->empleado_id)
                                ->where('anio', $vac->periodo_correspondiente)
                                ->first();

        if ($periodo) {
            $periodo->increment('dias_disponibles', $vac->dias_solicitados);
        }

        // 4. Borrar la solicitud
        $vac->delete();

        // 5. Redirigir con mensaje
        return redirect()
            ->route('vacaciones.index')
            ->with('success', 'Solicitud eliminada y días recuperados.');
    }

     
    public function destroyAdmin($id)
    {
        // Solo administradores pueden usar este método
        if (!auth()->user()->hasRole('administrador')) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }
    
        $vacation = SolicitudVacacion::with('empleado')->findOrFail($id);
    
        // Devolver los días al período si la solicitud estaba aprobada o pendiente
        if (in_array($vacation->estado, ['aprobado', 'pendiente'])) {
            $periodo = PeriodoVacacion::where('empleado_id', $vacation->empleado_id)
                ->where('anio', $vacation->periodo_correspondiente)
                ->first();
    
            if ($periodo) {
                $periodo->increment('dias_disponibles', $vacation->dias_solicitados);
            }
        }
    
        $nombreEmpleado = $vacation->empleado->name ?? 'N/A';
        $vacation->delete();
    
        return redirect()
            ->route('solicitudes_vacaciones.index')
            ->with('success', "Solicitud de {$nombreEmpleado} eliminada correctamente. Los días fueron devueltos al período.");
    }

}