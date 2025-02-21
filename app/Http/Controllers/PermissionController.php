<?php

namespace App\Http\Controllers;

use ZipArchive;
use Carbon\Carbon;
use App\Models\Department;
use App\Models\Permission;
use App\Jobs\GeneratePdfJob;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SolicitudPermiso;
use App\Exports\PermissionsExport;
use App\Http\Controllers\Controller;
use App\Jobs\ExportZipPaseSalidaJob;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ExportWeekPaseSalidaExcelJob;
use App\Notifications\PermissionRequested;
use App\Jobs\ExportPaseSalidaExcelLibroMJob;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NotificacionSeguridadPermiso;
use App\Notifications\PaseSalidaActualizadoNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'position' => 'required|string|max:255',
        'department_id' => 'required|exists:departments,id',
        'official_schedule' => 'required',
        'entry_exit_time' => 'required',
        'date' => 'required|date',
        'reason' => 'required|string',
        'supporting_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Validación del archivo
    ]);

    $documentPath = null;
    if ($request->hasFile('supporting_document')) {
        $documentPath = $request->file('supporting_document')->store('documents', 'public');
    }
    

    $permission = Permission::create([
        'user_id' => auth()->id(),
        'position' => $validated['position'],
        'department_id' => $validated['department_id'],
        'official_schedule' => $validated['official_schedule'],
        'entry_exit_time' => $validated['entry_exit_time'],
        'date' => $validated['date'],
        'reason' => $validated['reason'],
        'supporting_document' => $documentPath, // Guardar la ruta del archivo
        'status' => 'pendiente',
        'entry_exit_type' => $request->input('entry_exit_type'), // Nuevo campo
    ]);
    
    // Notificar al jefe directo
    $supervisor = auth()->user()->supervisor;
    if ($supervisor) {
    Notification::send($supervisor, new PermissionRequested($permission));
    }
    
    return redirect()->route('permissions.index')->with('success', 'Permiso solicitado exitosamente.');
}




public function index()
{
    $user = auth()->user();

    // Si el usuario es un supervisor, muestra los permisos de sus empleados
    if ($user->hasRole('jefe')) {
        $permissions = Permission::whereHas('user', function ($query) use ($user) {
            $query->where('supervisor_id', $user->id);
        })
        ->orWhere('user_id', $user->id) // Además, muestra los permisos que el mismo supervisor ha solicitado
        ->with('user', 'department')
        ->orderBy('date', 'desc') // Ordenar por fecha del permiso, ajusta si es necesario
        ->paginate(10); // Paginación de 10 permisos por página
    } else {
        // Si es un empleado, muestra solo sus propios permisos
        $permissions = Permission::where('user_id', $user->id)
            ->with('user', 'department')
            ->orderBy('date', 'desc')
            ->paginate(10); // Paginación de 10 permisos por página
    }

    return view('permissions.index', compact('permissions'));
}



    public function show(Permission $permission)
    {
        return view('permissions.show', compact('permission'));
    }

    
    
    public function approve(Permission $permission)
{
    // Actualizar estado a 'aprobado'
    $permission->update(['status' => 'aprobado']);

    // Obtener el empleado asociado al permiso
    $empleado = $permission->empleado;
    $empleado->notify(new PaseSalidaActualizadoNotification('aprobado'));

    if (!$empleado) {
        Log::error('El permiso no tiene un empleado asociado.');
        return redirect()->route('permissions.index')->with('error', 'Error: el permiso no tiene un empleado asociado.');
    }

    // Obtener el nombre del departamento si existe
    $departamento = Department::find($empleado->department_id);
    $nombreDepartamento = $departamento ? $departamento->name : 'No especificado';

    // Datos del solicitante a enviar en la notificación
    $data = [
        'nombre_empleado' => $empleado->name ?? 'No disponible',
        'puesto' => $empleado->puesto_empleado ?? 'No especificado',
        'departamento' => $nombreDepartamento ?? 'No especificado',
        'official_schedule' => $permission->official_schedule ?? 'No especificado',
        'entry_exit_time' => $permission->entry_exit_time ?? 'No especificado',
        'date' => $permission->date ?? 'No especificado',
        'reason' => $permission->reason ?? 'No especificado',
        'supporting_document' => $permission->supporting_document ?? 'No especificado',
        'status' => 'aprobado',
        'entry_exit_type' => $permission->entry_exit_type ?? 'No disponible',
    ];

    // Depuración: verificar si `$data` está bien
    Log::info('Datos de la notificación: ', $data);

    // Obtener usuarios con el rol de seguridad
    $usuariosSeguridad = User::whereHas('roles', function ($query) {
        $query->where('name', 'Seguridad');
    })->get();

    // Notificar a los usuarios de seguridad por correo
    foreach ($usuariosSeguridad as $seguridad) {
        $seguridad->notify(new NotificacionSeguridadPermiso($data));
    }

    return redirect()->route('permissions.index')->with('success', 'Permiso aprobado exitosamente.');
}

    


    
    public function reject(Permission $permission)
    {
        // Actualizar estado a 'rechazado'
        $permission->update(['status' => 'rechazado']);
    
        // Notificar al empleado
        $empleado = $permission->empleado; // Asegúrate de que exista la relación en el modelo
        $empleado->notify(new PaseSalidaActualizadoNotification('rechazado'));
    
        return redirect()->route('permissions.index')->with('success', 'Permiso rechazado exitosamente.');
    }
    

    public function create()
    {
        $departments = Department::all();

        return view('permissions.create', compact('departments'));
    }
    //RECURSOS HUMANOS
    // Mantén solo esta versión de indexHR
    

    public function indexRH(Request $request)
    {
    $query = Permission::with('user', 'department')
        ->whereIn('status', ['aprobado', 'rechazado', 'pendiente']);

   // Filtrar por nombre de empleado si se proporciona
   if ($request->filled('search')) {
    $search = $request->input('search');
    $query->whereHas('user', function ($q) use ($search) {
        $q->where('name', 'like', "%{$search}%");
    });
}

// Filtrar por rango de fechas si se proporciona
if ($request->filled('start_date') && $request->filled('end_date')) {
    $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
    $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
    $query->whereBetween('date', [$startDate, $endDate]);
}

// Si no se proporciona ni nombre ni rango de fechas, aplicar el filtro de la semana nominal
if (!$request->filled('search') && !$request->filled('start_date') && !$request->filled('end_date')) {
    // Miércoles de la semana actual
    $startOfWeek = Carbon::now()->startOfWeek(Carbon::WEDNESDAY); 
    // Martes de la próxima semana
    $endOfNextWeek = Carbon::now()->startOfWeek(Carbon::WEDNESDAY)->addDays(6);
    $query->whereBetween('date', [$startOfWeek, $endOfNextWeek]);
}

    // Obtener los permisos filtrados
    $permisos = $query->get();

    return view('rh.index', compact('permisos'));
    }
    


    //EXPORTAR LIBRO MAYO
    public function export(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');
        $userEmail = auth()->user()->email;
    
        // Despachar el Job para manejar la exportación y el envío de correo
        ExportPaseSalidaExcelLibroMJob::dispatch($search, $date, $userEmail);
    
        return back()->with('success', 'La exportación ha sido solicitada. Recibirás un correo con el enlace cuando esté lista.');
    }

    //EXPORTAR POR SEMANA
    public function exportWeek()
    {
     // Obtén el correo del usuario autenticado
     $userEmail = auth()->user()->email;

     // Despacha el Job
     ExportWeekPaseSalidaExcelJob::dispatch($userEmail);
 
     // Redirige con un mensaje de éxito
     return back()->with('success', 'La exportación semanal ha sido solicitada. Recibirás un correo con el enlace cuando esté lista.');
    }

    
    //Descargar PDF
    public function downloadPDF(Permission $permission)
    {
    $pdf = Pdf::loadView('permissions.pdf', compact('permission'));
    return $pdf->download('permiso_'.$permission->id.'.pdf');
    }
    //Descargar zip de todos los PDF´s
   
    
    
    public function downloadPDFsAsZip(Request $request)
    {
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = auth()->id();
        $userEmail = auth()->user()->email;
    
        // Despachar el Job
        ExportZipPaseSalidaJob::dispatch($search, $startDate, $endDate, $userId, $userEmail);

    
        return back()->with('success', 'La exportación en ZIP ha sido solicitada. Recibirás un correo con el enlace cuando esté lista.');
    }
    
   

}



