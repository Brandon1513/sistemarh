<?php

namespace App\Http\Controllers;

use App\Notifications\VacationSecurityNotification;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\PeriodoVacacion;
use App\Models\SolicitudVacacion;
use Illuminate\Support\Facades\Mail;
use App\Mail\VacationRequestNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VacationRHNotification;
use App\Notifications\VacationApprovedNotification;
use App\Notifications\VacationRejectedNotification;
use App\Models\VacationRequest;


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
    $periodos = PeriodoVacacion::where('empleado_id', $user->id)->get(); // Filtra los periodos solo del usuario autenticado
    $departments = Department::all(); // Obtiene todos los departamentos
    

    // Verifica que el usuario tenga un departamento asociado
    if ($user->departamento) {
        $departamentoNombre = $user->departamento->name;
    } else {
        $departamentoNombre = 'Sin departamento'; // Valor por defecto si el departamento es nulo
    }

    return view('vacaciones.create', compact('user', 'departments', 'departamentoNombre','periodos'));
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
        'fecha_inicio_vacaciones' => 'required|date',
        'fecha_termino_vacaciones' => 'required|date|after_or_equal:fecha_inicio_vacaciones',
        'periodo_correspondiente' => 'required|integer',
    ]);

    // Buscar el periodo seleccionado
    $periodo = PeriodoVacacion::where('empleado_id', $user->id)
                               ->where('anio', $request->periodo_correspondiente)
                               ->first();

    // Verificar si el empleado tiene suficientes días disponibles en el período seleccionado
    if (!$periodo || $periodo->dias_disponibles < $request->dias_solicitados) {
        return redirect()->back()->withErrors('No tienes suficientes días disponibles para este período.');
    }

    // Crear la solicitud de vacaciones y actualizar los días disponibles en el período
    $periodo->dias_disponibles -= $request->dias_solicitados;
    $periodo->save();

    // Crear la solicitud de vacaciones en la base de datos
    $vacationRequest = SolicitudVacacion::create([
        'empleado_id' => $user->id,
        'departamento_id' => $user->departamento_id,
        'fecha_solicitud' => now(),
        'dias_corresponden' => $periodo->dias_corresponden,
        'dias_solicitados' => $request->dias_solicitados,
        'dias_pendientes' => $periodo->dias_disponibles,
        'periodo_correspondiente' => $periodo->anio,
        'fecha_inicio' => $request->fecha_inicio_vacaciones,
        'fecha_fin' => $request->fecha_termino_vacaciones,
        'fecha_reincorporacion' => Carbon::parse($request->fecha_termino_vacaciones)->addDay(),
        'estado' => 'pendiente',
    ]);

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

    $vacationRequest = SolicitudVacacion::findOrFail($id);
    $vacationRequest->estado = 'rechazado';
    $vacationRequest->save();

    $employee = $vacationRequest->empleado;
    $rhEmails = User::role('recursos_humanos')->pluck('email')->toArray();

    // Notificar al empleado
    $employee->notify(new VacationRejectedNotification($vacationRequest));

    // Notificar al departamento de Recursos Humanos
    Notification::route('mail', $rhEmails)->notify(new VacationRHNotification($vacationRequest, 'rechazada'));

    return redirect()->route('vacaciones.index')->with('success', 'Solicitud rechazada.');
}



    
}
