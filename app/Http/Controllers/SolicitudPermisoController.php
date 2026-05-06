<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Jobs\ExportPermisosJob;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SolicitudPermiso;
use App\Jobs\ExportZipPermisosJob;
use App\Jobs\ExportWeekPermisosJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionSolicitudPermiso;

class SolicitudPermisoController extends Controller
{
    /**
     * Lista de solicitudes de permiso.
     * Por defecto filtra la semana nominal actual (miércoles → martes).
     * Acepta filtros: search, estado, tipo, start_date, end_date.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query según rol
        if ($user->hasRole('jefe')) {
            $query = SolicitudPermiso::with('empleado', 'departamento')
                ->where(function ($q) use ($user) {
                    $q->whereHas('empleado', fn($sq) => $sq->where('supervisor_id', $user->id))
                      ->orWhere('empleado_id', $user->id);
                });
        } else {
            $query = SolicitudPermiso::with('empleado', 'departamento')
                ->where('empleado_id', $user->id);
        }

        // Filtro búsqueda por nombre
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('empleado', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        // Filtro por tipo de solicitud
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        // Filtro por rango de fechas — si no hay filtros aplica semana nominal
        $hasDateFilter = $request->filled('start_date') && $request->filled('end_date');
        $hasAnyFilter  = $request->filled('search') || $request->filled('estado') || $request->filled('tipo') || $hasDateFilter;

        if ($hasDateFilter) {
            $query->whereBetween('fecha_inicio', [
                Carbon::parse($request->input('start_date'))->startOfDay(),
                Carbon::parse($request->input('end_date'))->endOfDay(),
            ]);
        } elseif (!$hasAnyFilter) {
            // Semana nominal: miércoles actual → martes siguiente
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::WEDNESDAY);
            $endOfWeek   = Carbon::now()->addWeek()->startOfWeek(Carbon::TUESDAY)->endOfDay();
            $query->whereBetween('fecha_inicio', [$startOfWeek, $endOfWeek]);
        }

        $solicitudes = $query->orderBy('fecha_inicio', 'desc')->paginate(10)->withQueryString();

        return view('permisos.index', compact('solicitudes'));
    }

    /**
     * Formulario de nueva solicitud.
     * Pasa $authUser para autocompletar departamento.
     */
    public function create()
    {
        $departamentos = Department::all();
        $authUser      = Auth::user()->load('departamento');

        return view('permisos.create', compact('departamentos', 'authUser'));
    }

    /**
     * Guarda la solicitud y notifica al supervisor.
     */
    public function store(Request $request)
    {
        $request->validate([
            'departamento_id'      => 'required|exists:departments,id',
            'fecha_inicio'         => 'required|date',
            'fecha_termino'        => 'required|date|after_or_equal:fecha_inicio',
            'motivo'               => 'required|string|max:500',
            'tipo_permiso'         => 'required|string|in:Con Goce de Sueldo,Sin Goce de Sueldo',
            'fecha_regreso_laborar'=> 'nullable|date|after_or_equal:fecha_termino',
            'tipo'                 => 'required|in:Permiso,Comisión,Suspensión',
            'dia_descanso'         => 'nullable|string',
            'archivo'              => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'empleado_id'          => auth()->id(),
            'departamento_id'      => $request->departamento_id,
            'fecha_inicio'         => $request->fecha_inicio,
            'fecha_termino'        => $request->fecha_termino,
            'motivo'               => $request->motivo,
            'tipo_permiso'         => $request->tipo_permiso,
            'estado'               => 'pendiente',
            'fecha_regreso_laborar'=> $request->fecha_regreso_laborar,
            'tipo'                 => $request->tipo,
            'dia_descanso'         => $request->dia_descanso,
        ];

        if ($request->hasFile('archivo')) {
            $data['archivo'] = $request->file('archivo')->store('archivos_permisos', 'public');
        }

        $permiso = SolicitudPermiso::create($data);

        // Notificar al supervisor
        $supervisor = Auth::user()->supervisor;
        if ($supervisor && $supervisor->email) {
            Mail::to($supervisor->email)->send(new NotificacionSolicitudPermiso($permiso));
        }

        return redirect()->route('permisos.index')
            ->with('success', 'Solicitud enviada y notificada al supervisor.');
    }

    /**
     * Detalle de una solicitud.
     */
    public function show($id)
    {
        $solicitud = SolicitudPermiso::with('empleado', 'departamento')->findOrFail($id);
        return view('permisos.show', compact('solicitud'));
    }

    /**
     * Aprobar solicitud.
     */
    public function aprobar($id)
    {
        $permiso = SolicitudPermiso::with('empleado')->findOrFail($id);

        if ($permiso->empleado_id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes aprobar tu propio permiso.');
        }

        if ($permiso->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'El permiso ya fue procesado.');
        }

        $permiso->update(['estado' => 'aprobado']);

        if ($permiso->empleado) {
            $permiso->empleado->notify(new \App\Notifications\EstadoPermisoActualizadoNotification('aprobado'));
        }

        return redirect()->route('permisos.index')->with('success', 'Permiso aprobado exitosamente.');
    }

    /**
     * Rechazar solicitud.
     */
    public function rechazar($id)
    {
        $permiso = SolicitudPermiso::with('empleado')->findOrFail($id);

        if ($permiso->empleado_id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes rechazar tu propio permiso.');
        }

        if ($permiso->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'El permiso ya fue procesado.');
        }

        $permiso->update(['estado' => 'rechazado']);

        if ($permiso->empleado) {
            $permiso->empleado->notify(new \App\Notifications\EstadoPermisoActualizadoNotification('rechazado'));
        }

        return redirect()->route('permisos.index')->with('success', 'Permiso rechazado exitosamente.');
    }

    // ─── Vista RH ─────────────────────────────────────────────────────────────

    public function indexSolicitudesPermiso(Request $request)
    {
        $query = SolicitudPermiso::with('empleado', 'departamento')
            ->whereIn('estado', ['aprobado', 'rechazado', 'pendiente']);

        $hasFilters = $request->filled('search') ||
            ($request->filled('start_date') && $request->filled('end_date'));

        if (!$hasFilters) {
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::WEDNESDAY);
            $endOfWeek   = Carbon::now()->addWeek()->startOfWeek(Carbon::TUESDAY)->endOfDay();
            $query->whereBetween('fecha_inicio', [$startOfWeek, $endOfWeek]);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('empleado', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('fecha_inicio', [
                Carbon::parse($request->input('start_date'))->startOfDay(),
                Carbon::parse($request->input('end_date'))->endOfDay(),
            ]);
        }

        $permisos = $query->orderBy('fecha_inicio', 'desc')->get();

        return view('solicitudes_permisos.index', compact('permisos'));
    }

    // ─── Exportaciones ────────────────────────────────────────────────────────

    public function downloadPDF($id)
    {
        $permiso = SolicitudPermiso::with('empleado', 'departamento')->findOrFail($id);
        $pdf = Pdf::loadView('solicitudes_permisos.pdf', compact('permiso'));
        return $pdf->download('permiso_' . $permiso->id . '.pdf');
    }

    public function export(Request $request)
    {
        ExportPermisosJob::dispatch(
            $request->input('search'),
            $request->input('start_date'),
            $request->input('end_date'),
            auth()->id(),
            auth()->user()->email
        );
        return redirect()->back()->with('success', 'Exportación en proceso. Recibirás una notificación cuando esté lista.');
    }

    public function exportWeek()
    {
        ExportWeekPermisosJob::dispatch(auth()->id(), auth()->user()->email);
        return redirect()->back()->with('success', 'Exportación semanal en proceso. Recibirás una notificación cuando esté lista.');
    }

    public function downloadSolicitudesZIP(Request $request)
    {
        ExportZipPermisosJob::dispatch(
            $request->input('search'),
            $request->input('start_date'),
            $request->input('end_date'),
            auth()->id(),
            auth()->user()->email
        );
        return redirect()->back()->with('success', 'Exportación ZIP en proceso. Recibirás un correo cuando esté lista.');
    }
}