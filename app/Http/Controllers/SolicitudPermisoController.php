<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\PermisosExport;
use App\Jobs\ExportPermisosJob;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SolicitudPermiso;
use App\Jobs\ExportZipPermisosJob;
use App\Jobs\ExportWeekPermisosJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\NotificacionSolicitudPermiso;

class SolicitudPermisoController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Obtener el usuario autenticado
    
        // Definir el rango de la semana nominativa
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::WEDNESDAY); // Inicio desde el miércoles de esta semana
        $endOfWeek = Carbon::now()->addWeek()->startOfWeek(Carbon::TUESDAY)->endOfDay(); // Fin hasta el martes de la siguiente semana
    
        if ($user->hasRole('jefe')) {
            // Si el usuario es jefe, muestra los permisos de los empleados que supervisa y los suyos propios
            $solicitudes = SolicitudPermiso::whereHas('empleado', function ($query) use ($user) {
                    $query->where('supervisor_id', $user->id); // Filtra los empleados supervisados
                })
                ->orWhere('empleado_id', $user->id) // Muestra los permisos del propio jefe
                ->whereBetween('fecha_inicio', [$startOfWeek, $endOfWeek]) // Filtrar por la semana nominativa
                ->with('empleado', 'departamento') // Cargar las relaciones
                ->orderBy('fecha_inicio', 'desc') // Ordenar por fecha de inicio
                ->paginate(10); // Paginación de 10 elementos por página
        } else {
            // Si es un empleado, solo muestra sus permisos
            $solicitudes = SolicitudPermiso::where('empleado_id', $user->id)
                ->whereBetween('fecha_inicio', [$startOfWeek, $endOfWeek]) // Filtrar por la semana nominativa
                ->with('empleado', 'departamento')
                ->orderBy('fecha_inicio', 'desc')
                ->paginate(10);
        }
    
        // Retorna la vista con los permisos filtrados y paginados
        return view('permisos.index', compact('solicitudes'));
    }

    public function create()
    {
        // Obtener todos los departamentos
        $departamentos = Department::all();

        // Retornar la vista y pasar los departamentos
        return view('permisos.create', compact('departamentos'));
    }
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'departamento_id' => 'required|exists:departments,id',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'required|string|max:255',
            'tipo_permiso' => 'required|string|in:Con Goce de Sueldo,Sin Goce de Sueldo',
            'fecha_regreso_laborar' => 'nullable|date|after_or_equal:fecha_termino',
            'tipo' => 'required|in:Permiso,Comisión,Suspensión',
            'dia_descanso' => 'nullable|string',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // nueva validación
        ]);

        // Inicializar el array de datos
        $data = [
            'empleado_id' => auth()->id(),
            'departamento_id' => $request->departamento_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_termino' => $request->fecha_termino,
            'motivo' => $request->motivo,
            'tipo_permiso' => $request->tipo_permiso,
            'estado' => 'pendiente',
            'fecha_regreso_laborar' => $request->fecha_regreso_laborar,
            'tipo' => $request->tipo,
            'dia_descanso' => $request->dia_descanso,
        ];

        // Si se subió un archivo, guardarlo
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo')->store('archivos_permisos', 'public');
            $data['archivo'] = $archivo;
        }

        // Crear la solicitud de permiso
        $permiso = SolicitudPermiso::create($data);

        // Obtener al supervisor del empleado actual
        $supervisor = Auth::user()->supervisor;

        // Verificar que el supervisor existe y enviar el correo
        if ($supervisor && $supervisor->email) {
            Mail::to($supervisor->email)->send(new NotificacionSolicitudPermiso($permiso));
        }

        // Redirigir con mensaje de éxito
        return redirect()->route('permisos.index')->with('success', 'La solicitud de permiso ha sido enviada y notificada al supervisor.');
    }

    public function show($id)
    {
        // Buscar la solicitud de permiso por su ID
        $solicitud = SolicitudPermiso::with('empleado', 'departamento')->findOrFail($id);

        // Retornar la vista con la solicitud de permiso
        return view('permisos.show', compact('solicitud'));
    }

    public function aprobar($id)
    {
        $permiso = SolicitudPermiso::with('empleado')->findOrFail($id);

        // Validar si el jefe está intentando aprobar su propio permiso
        if ($permiso->empleado_id === auth()->id()) {
            return redirect()->back()->with('error', 'No está autorizado para aprobar su propio permiso.');
        }

        if ($permiso->estado != 'pendiente') {
            return redirect()->back()->with('error', 'El permiso ya ha sido procesado.');
        }

        $permiso->estado = 'aprobado';
        $permiso->save();

        $empleado = $permiso->empleado;
        if ($empleado) {
            $empleado->notify(new \App\Notifications\EstadoPermisoActualizadoNotification('aprobado'));
        }

        return redirect()->route('permisos.index')->with('success', 'Permiso aprobado con éxito.');
    }

public function rechazar($id)
    {
        $permiso = SolicitudPermiso::with('empleado')->findOrFail($id);

        // Validar si el jefe está intentando rechazar su propio permiso
        if ($permiso->empleado_id === auth()->id()) {
            return redirect()->back()->with('error', 'No está autorizado para rechazar su propio permiso.');
        }

        if ($permiso->estado != 'pendiente') {
            return redirect()->back()->with('error', 'El permiso ya ha sido procesado.');
        }

        $permiso->estado = 'rechazado';
        $permiso->save();

        $empleado = $permiso->empleado;
        if ($empleado) {
            $empleado->notify(new \App\Notifications\EstadoPermisoActualizadoNotification('rechazado'));
        }

        return redirect()->route('permisos.index')->with('success', 'Permiso rechazado con éxito.');
    }



    public function indexSolicitudesPermiso(Request $request)
{
    // Definir el rango de la semana nominativa (de miércoles a martes)
    $startOfWeek = Carbon::now()->startOfWeek(Carbon::WEDNESDAY); // Desde el miércoles de esta semana
    $endOfWeek = Carbon::now()->addWeek()->startOfWeek(Carbon::TUESDAY)->endOfDay(); // Hasta el martes de la siguiente semana

    // Construir la consulta base
    $query = SolicitudPermiso::with('empleado', 'departamento')
        ->whereIn('estado', ['aprobado', 'rechazado', 'pendiente']);

    // Verificar si hay filtros aplicados
    $hasFilters = $request->filled('search') || ($request->filled('start_date') && $request->filled('end_date'));

    // Si no hay filtros, aplicar el filtro de la semana nominativa
    if (!$hasFilters) {
        $query->whereBetween('fecha_inicio', [$startOfWeek, $endOfWeek]);
    }

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

    // Obtener los permisos filtrados
    $permisos = $query->get();

    // Retornar la vista con los permisos
    return view('solicitudes_permisos.index', compact('permisos'));
}



public function downloadPDF($id)
{
    // Obtener el permiso por su ID
    $permiso = SolicitudPermiso::with('empleado', 'departamento')->findOrFail($id);

    // Generar el PDF utilizando una vista de Blade
    $pdf = Pdf::loadView('solicitudes_permisos.pdf', compact('permiso'));

    // Retornar la descarga del archivo PDF
    return $pdf->download('permiso_'.$permiso->id.'.pdf');
}

 //EXPORTAR LIBRO MAYO
 public function export(Request $request)
 {
     $search = $request->input('search');
     $startDate = $request->input('start_date');
     $endDate = $request->input('end_date');
 
     // Encolar el job para exportar en segundo plano
     ExportPermisosJob::dispatch($search, $startDate, $endDate, auth()->id(), auth()->user()->email);


 
     return redirect()->back()->with('success', 'La exportación se está procesando. Recibirás una notificación cuando esté lista.');
 }
 

//Exporta por seman nominal miercoles a martes
public function exportWeek()
{
    // Obtener el ID y el email del usuario autenticado
    $userId = auth()->id();
    $userEmail = auth()->user()->email;

    // Encolar el Job para exportar en segundo plano
    ExportWeekPermisosJob::dispatch($userId, $userEmail);

    // Redirigir con mensaje de éxito
    return redirect()->back()->with('success', 'La exportación de la semana nominal se está procesando. Recibirás una notificación cuando esté lista.');
}


    public function downloadSolicitudesZIP(Request $request)
{
    $search = $request->input('search');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $userId = auth()->id();
    $userEmail = auth()->user()->email;

    // Llamar al Job
    ExportZipPermisosJob::dispatch($search, $startDate, $endDate, $userId, $userEmail);

    return redirect()->back()->with('success', 'La exportación en ZIP se está procesando. Recibirás un correo cuando esté lista.');
}


 

}
