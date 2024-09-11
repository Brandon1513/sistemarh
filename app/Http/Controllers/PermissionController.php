<?php

namespace App\Http\Controllers;

use ZipArchive;
use Carbon\Carbon;
use App\Models\Department;
use App\Models\Permission;
use App\Jobs\GeneratePdfJob;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PermissionsExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Notifications\PermissionRequested;
use Illuminate\Support\Facades\Notification;


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
        })->orWhere('user_id', $user->id) // Además, muestra los permisos que el mismo supervisor ha solicitado
        ->with('user', 'department')
        ->get();
    } else {
        // Si es un empleado, muestra solo sus propios permisos
        $permissions = Permission::where('user_id', $user->id)
            ->with('user', 'department')
            ->get();
    }

    return view('permissions.index', compact('permissions'));
    }


    public function show(Permission $permission)
    {
        return view('permissions.show', compact('permission'));
    }

    public function approve(Permission $permission)
    {
        $permission->update(['status' => 'aprobado']);
        return redirect()->route('permissions.index')->with('success', 'Permiso aprobado exitosamente.');
    }

    public function reject(Permission $permission)
    {
        $permission->update(['status' => 'rechazado']);
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
    // Construir la consulta base
    $query = Permission::with('user', 'department');

    // Filtrar por nombre de empleado si se proporciona
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->whereHas('user', function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });
    }

    // Filtrar por fecha si se proporciona
    if ($request->filled('date')) {
        $date = $request->input('date');
        $query->whereDate('date', $date);
    }

    // Si no se proporciona ni nombre ni fecha, descargar permisos de la semana actual
    //if (!$request->filled('search') && !$request->filled('date')) {
    //    $startOfWeek = Carbon::now()->startOfWeek()->addDays(1); // Martes de esta semana
    //    $endOfNextWeek = Carbon::now()->startOfWeek()->addDays(10); // Jueves de la próxima semana
    //    $query->whereBetween('date', [$startOfWeek, $endOfNextWeek]);
    //}

    // Obtener los permisos filtrados
    $permissions = $query->get();

    // Descargar el archivo Excel con los permisos filtrados
    return Excel::download(new PermissionsExport($permissions), 'permissions_filtered.xlsx');
    }

    //EXPORTAR POR SEMANA
    public function exportWeek()
    {
    $startOfWeek = Carbon::now()->startOfWeek()->addDays(1); // Martes de esta semana
    $endOfNextWeek = Carbon::now()->startOfWeek()->addDays(10); // Jueves de la próxima semana

    $permissions = Permission::with('user.supervisor')
        ->whereBetween('date', [$startOfWeek, $endOfNextWeek])
        ->get();

    return Excel::download(new PermissionsExport($permissions), 'permissions_week.xlsx');
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
    // Validar las fechas ingresadas
    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Obtener los permisos en el rango de fechas
    $permissions = Permission::whereBetween('date', [$startDate, $endDate])->get();

    if ($permissions->isEmpty()) {
        return redirect()->back()->with('error', 'No se encontraron permisos en el rango de fechas seleccionado.');
    }

    // Crear un archivo ZIP en una ubicación temporal
    $zip = new ZipArchive;
    $zipFileName = 'permisos_' . $startDate . '_a_' . $endDate . '.zip';
    $zipPath = storage_path($zipFileName);

    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
        // Iterar sobre los permisos y generar un PDF por cada uno
        foreach ($permissions as $permission) {
            // Generar el PDF para cada permiso
            $pdf = Pdf::loadView('permissions.pdf', compact('permission'));
            $pdfOutput = $pdf->output();
            
            // Crear un nombre de archivo único para cada PDF en el ZIP
            $pdfFileName = 'permiso_' . $permission->id . '.pdf';
            
            // Añadir el archivo PDF al ZIP
            $zip->addFromString($pdfFileName, $pdfOutput);
        }

        // Cerrar el archivo ZIP
        $zip->close();

        // Descargar el archivo ZIP
        return response()->download($zipPath)->deleteFileAfterSend(true);
    } else {
        return redirect()->back()->with('error', 'Error al generar el archivo ZIP.');
    }
    }
    
   

}



