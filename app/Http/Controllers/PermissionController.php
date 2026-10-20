<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Department;
use App\Models\Permission;
use App\Jobs\GeneratePdfJob;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PermissionsExport;
use App\Jobs\ExportZipPaseSalidaJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Jobs\ExportWeekPaseSalidaExcelJob;
use App\Jobs\ExportPaseSalidaExcelLibroMJob;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Services\PasesService;

class PermissionController extends Controller
{
    /**
     * Lista de permisos — empleado ve los suyos, jefe ve los de su equipo.
     * Acepta filtros: search, status, tipo (entry_exit_type).
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Base query según rol
        if ($user->hasRole('jefe')) {
            $query = Permission::with('user', 'department')
                ->where(function ($q) use ($user) {
                    $q->whereHas('user', fn($sq) => $sq->where('supervisor_id', $user->id))
                      ->orWhere('user_id', $user->id);
                });
        } else {
            $query = Permission::with('user', 'department')
                ->where('user_id', $user->id);
        }

        // Filtro búsqueda por nombre de empleado
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filtro por tipo entrada/salida
        if ($request->filled('tipo')) {
            $query->where('entry_exit_type', $request->input('tipo'));
        }

        $permissions = $query->orderBy('date', 'desc')->paginate(10)->withQueryString();

        return view('permissions.index', compact('permissions'));
    }

    /**
     * Formulario de solicitud.
     */
    public function create()
    {
        $departments = Department::all();
        $authUser    = auth()->user()->load('departamento');
        return view('permissions.create', compact('departments', 'authUser'));
    }

    /**
     * Guarda un nuevo permiso.
     */
    public function store(Request $request, PasesService $pases)
    {
        $datos = $request->validate(PasesService::reglas() + [
            'position'      => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $pases->solicitar(auth()->user(), $datos, $request->file('supporting_document'));

        return redirect()->route('permissions.index')->with('success', 'Permiso solicitado exitosamente.');
    }

    /**
     * Detalle de un permiso.
     */
   public function show(Permission $permission)
    {
        Gate::authorize('view', $permission);
        return view('permissions.show', compact('permission'));
    }

    /**
     * Aprobar un permiso y notificar a seguridad.
     */
    public function approve(Permission $permission, PasesService $pases)
    {
        Gate::authorize('approve', $permission);

        $pases->aprobar($permission, auth()->user());

        return redirect()->route('permissions.index')->with('success', 'Permiso aprobado exitosamente.');
    }

    /**
     * Rechazar un permiso.
     */
    public function reject(Permission $permission, PasesService $pases)
    {
        Gate::authorize('reject', $permission);

        $pases->rechazar($permission, auth()->user());

        return redirect()->route('permissions.index')->with('success', 'Permiso rechazado exitosamente.');
    }

    // ─── Recursos Humanos ─────────────────────────────────────────────────────

    /**
     * Vista RH — muestra todos los permisos con filtros avanzados.
     * Por defecto muestra la semana nominal actual (miércoles→martes).
     */
    public function indexRH(Request $request)
    {
        $query = Permission::with('user', 'department')
            ->whereIn('status', ['aprobado', 'rechazado', 'pendiente']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [
                Carbon::parse($request->input('start_date'))->startOfDay(),
                Carbon::parse($request->input('end_date'))->endOfDay(),
            ]);
        } elseif (!$request->filled('search')) {
            // Semana nominal: miércoles actual → martes siguiente
            $startOfWeek  = Carbon::now()->startOfWeek(Carbon::WEDNESDAY);
            $endOfNextWeek = (clone $startOfWeek)->addDays(6);
            $query->whereBetween('date', [$startOfWeek, $endOfNextWeek]);
        }

        $permisos = $query->orderBy('date', 'desc')->get();

        return view('rh.index', compact('permisos'));
    }

    // ─── Exportaciones ────────────────────────────────────────────────────────

    public function export(Request $request)
    {
        $userEmail = auth()->user()->email;
        ExportPaseSalidaExcelLibroMJob::dispatch(
            $request->input('search'),
            $request->input('start_date'),
            $request->input('end_date'),
            $userEmail
        );
        return back()->with('success', 'Exportación solicitada. Recibirás un correo cuando esté lista.');
    }

    public function exportWeek()
    {
        ExportWeekPaseSalidaExcelJob::dispatch(auth()->user()->email);
        return back()->with('success', 'Exportación semanal solicitada. Recibirás un correo cuando esté lista.');
    }

    public function downloadPDF(Permission $permission)
    {
        $pdf = Pdf::loadView('permissions.pdf', compact('permission'));
        return $pdf->download('permiso_' . $permission->id . '.pdf');
    }

    public function downloadPDFsAsZip(Request $request)
    {
        ExportZipPaseSalidaJob::dispatch(
            $request->input('search'),
            $request->input('start_date'),
            $request->input('end_date'),
            auth()->id(),
            auth()->user()->email
        );
        return back()->with('success', 'Exportación ZIP solicitada. Recibirás un correo cuando esté lista.');
    }
}