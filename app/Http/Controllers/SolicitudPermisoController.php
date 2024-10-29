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

    // Definir el rango de la semana nominativa
    $startOfWeek = Carbon::now()->startOfWeek(Carbon::WEDNESDAY); // Inicio desde el miércoles de esta semana
    $endOfWeek = Carbon::now()->addWeek()->startOfWeek(Carbon::TUESDAY)->endOfDay(); // Fin hasta el martes de la siguiente semana

    if ($user->hasRole('jefe')) {
        // Si el usuario es jefe, muestra los permisos de los empleados que supervisa y los suyos propios
        $solicitudes = SolicitudPermiso::whereHas('empleado', function ($query) use ($user) {
                $query->where('supervisor_id', $user->id);  // Filtra los empleados supervisados
            })
            ->orWhere('empleado_id', $user->id)  // Muestra los permisos del propio jefe
            ->whereBetween('fecha_inicio', [$startOfWeek, $endOfWeek])  // Filtrar por la semana nominativa
            ->with('empleado', 'departamento')  // Cargar las relaciones
            ->get();
    } else {
        // Si es un empleado, solo muestra sus permisos
        $solicitudes = SolicitudPermiso::where('empleado_id', $user->id)
            ->whereBetween('fecha_inicio', [$startOfWeek, $endOfWeek])  // Filtrar por la semana nominativa
            ->with('empleado', 'departamento')
            ->get();
    }

    // Retorna la vista con los permisos filtrados por la semana nominativa
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
    // Construir la consulta base con las relaciones necesarias
    $query = SolicitudPermiso::with('empleado', 'departamento');

    // **Filtro por nombre del empleado** si se proporciona
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->whereHas('empleado', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });
    }

    // **Filtro por rango de fechas** si se proporciona
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        $query->whereBetween('fecha_inicio', [$startDate, $endDate]);
    }

    // Obtener los permisos filtrados
    $solicitudes = $query->get();

    // Descargar los permisos filtrados en un archivo Excel
    return Excel::download(new PermisosExport($solicitudes), 'controlausencia.xlsx');
}

//Exporta por seman nominal miercoles a martes
    public function exportWeek()
    {
        // Obtener el inicio y fin de la semana nominativa
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::WEDNESDAY); // Desde miércoles de esta semana
        $endOfWeek = Carbon::now()->addWeek()->startOfWeek(Carbon::TUESDAY)->endOfDay(); // Hasta martes de la siguiente semana

        // Filtrar las solicitudes de permisos en ese rango
        $solicitudes = SolicitudPermiso::with('empleado', 'departamento')
            ->whereBetween('fecha_inicio', [$startOfWeek, $endOfWeek])
            ->get();

        // Descargar los permisos de la semana nominativa en un archivo Excel
        return Excel::download(new PermisosExport($solicitudes), 'permisos_semana_nominativa.xlsx');
    }

    public function downloadSolicitudesZIP(Request $request)
{
    // Crear una nueva instancia de ZipArchive
    $zip = new \ZipArchive();
    $fileName = 'solicitudes_permisos.zip';

    // Ruta donde se almacenará el archivo ZIP temporalmente
    $zipPath = public_path($fileName);

    if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
        // Construir la consulta base
        $query = SolicitudPermiso::with('empleado', 'departamento')
            ->whereIn('estado', ['aprobado', 'rechazado', 'pendiente']);

        // Filtrar por nombre si se proporciona
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

        // Generar PDFs y agregarlos al ZIP
        foreach ($permisos as $permiso) {
            // Generar el PDF para el permiso y pasar la variable correctamente
            $pdf = Pdf::loadView('solicitudes_permisos.pdf', ['permiso' => $permiso]);

            // Guardar el contenido del PDF como una cadena
            $pdfOutput = $pdf->output();

            // Añadir el PDF al archivo ZIP (usa el ID del permiso para el nombre del archivo)
            $zip->addFromString('permiso_'.$permiso->id.'.pdf', $pdfOutput);
        }

        // Cerrar el archivo ZIP después de agregar todos los PDFs
        $zip->close();
    }

    // Retornar el archivo ZIP para descarga
    return response()->download($zipPath)->deleteFileAfterSend(true);
}


 

}
