<?php

namespace App\Http\Controllers;

use App\Exports\PermisosExport;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\SolicitudPermiso;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionSolicitudPermiso;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class SolicitudPermisoController extends Controller
{
    public function index()
    {
        $user = Auth::user();  // Obtener el usuario autenticado

        if ($user->hasRole('jefe')) {
            // Si el usuario es jefe, muestra los permisos de los empleados que supervisa y los suyos propios
            $solicitudes = SolicitudPermiso::whereHas('empleado', function ($query) use ($user) {
                    $query->where('supervisor_id', $user->id);  // Filtra los empleados supervisados
                })
                ->orWhere('empleado_id', $user->id)  // Muestra los permisos del propio jefe
                ->with('empleado', 'departamento')  // Cargar las relaciones
                ->get();
        } else {
            // Si es un empleado, solo muestra sus permisos
            $solicitudes = SolicitudPermiso::where('empleado_id', $user->id)
                ->with('empleado', 'departamento')
                ->get();
        }
    
        // Retorna la vista con los permisos filtrados
        return view('permisos.index', compact(var_name: 'solicitudes'));

        
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
        ]);
    
        // Crear la solicitud de permiso
        $permiso = SolicitudPermiso::create([
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
        ]);
    
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
        // Buscar la solicitud de permiso por su ID
        $permiso = SolicitudPermiso::findOrFail($id);

        // Verificar si el permiso ya está aprobado o rechazado
        if ($permiso->estado != 'pendiente') {
            return redirect()->back()->with('error', 'El permiso ya ha sido procesado.');
        }

        // Cambiar el estado a "aprobado"
        $permiso->estado = 'aprobado';
        $permiso->save();

        // Redirigir con mensaje de éxito
        return redirect()->route('permisos.index')->with('success', 'Permiso aprobado con éxito.');
    }

    public function rechazar($id)
    {
        // Buscar la solicitud de permiso por su ID
        $permiso = SolicitudPermiso::findOrFail($id);

        // Verificar si el permiso ya está aprobado o rechazado
        if ($permiso->estado != 'pendiente') {
            return redirect()->back()->with('error', 'El permiso ya ha sido procesado.');
        }

        // Cambiar el estado a "rechazado"
        $permiso->estado = 'rechazado';
        $permiso->save();

        // Redirigir con mensaje de éxito
        return redirect()->route('permisos.index')->with('success', 'Permiso rechazado con éxito.');
    }

    public function indexSolicitudesPermiso(Request $request)
{
    $query = SolicitudPermiso::with('empleado', 'departamento')
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

    // Obtener los permisos filtrados
    $permisos = $query->get();

    return view('solicitudes_permisos.index', compact('permisos'));
}

public function downloadPDF($id)
{
    // Obtener el permiso por ID
    $permiso = SolicitudPermiso::findOrFail($id);

    // Generar el PDF utilizando una vista de Blade
    $pdf = Pdf::loadView('solicitudes_permisos.pdf', compact('permiso'));

    // Retornar la descarga del archivo PDF
    return $pdf->download('permiso_'.$permiso->id.'.pdf');
}

 //EXPORTAR LIBRO MAYO
 public function export(Request $request)
{
    // Construir la consulta base con las relaciones necesarias
    $query = SolicitudPermiso::with('user', 'department');

    // **Filtro por nombre del empleado** si se proporciona
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });
    }

    // **Filtro por fecha específica** si se proporciona
    if ($request->filled('date')) {
        $date = $request->input('date');
        $query->whereDate('fecha_inicio', $date)
              ->orWhereDate('fecha_termino', $date);
    }

    // **Si no se proporciona ni nombre ni fecha**, descargar los permisos de la semana actual
    if (!$request->filled('search') && !$request->filled('date')) {
        $startOfWeek = Carbon::now()->startOfWeek();  // Lunes
        $endOfWeek = Carbon::now()->endOfWeek();      // Domingo
        $query->whereBetween('fecha_inicio', [$startOfWeek, $endOfWeek]);
    }

    // Obtener los permisos filtrados
    $SolicitudPermiso = $query->get();

    // Descargar los permisos en un archivo Excel
    return Excel::download(new PermisosExport(permisos: $SolicitudPermiso), 'controlausencia.xlsx');
}

}
