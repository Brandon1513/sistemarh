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
use Illuminate\Support\Facades\Gate;
use App\Services\PermisosService;


class SolicitudPermisoController extends Controller
{
    /**
     * Lista de solicitudes de permiso del empleado (o de su equipo si es jefe).
     *
     * Vistas rápidas (?vista=):
     *   - todas              Todas las solicitudes (por defecto para empleados)
     *   - pendientes         Solo pendientes, sin importar la fecha
     *   - semana             Permisos con algún día en la semana nominal
     *   - semana_pendientes  Semana nominal + todas las pendientes (por defecto para jefes)
     * Filtros adicionales: search, estado, tipo, start_date, end_date.
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $esJefe = $user->hasRole('jefe');

        // Base query según rol
        if ($esJefe) {
            $query = SolicitudPermiso::with('empleado', 'departamento')
                ->where(function ($q) use ($user) {
                    $q->whereHas('empleado', fn($sq) => $sq->where('supervisor_id', $user->id))
                      ->orWhere('empleado_id', $user->id);
                });
        } else {
            $query = SolicitudPermiso::with('empleado', 'departamento')
                ->where('empleado_id', $user->id);
        }

        $pendientes = (clone $query)->where('estado', 'pendiente')->count();

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

        $vistas = $esJefe
            ? ['semana_pendientes' => 'Esta semana y pendientes', 'pendientes' => 'Pendientes', 'todas' => 'Todas']
            : ['todas' => 'Todas', 'pendientes' => 'Pendientes', 'semana' => 'Esta semana'];

        $vista = $this->aplicarVistaOFechas($query, $request, $vistas, $esJefe ? 'semana_pendientes' : 'todas');

        $solicitudes = $query->orderBy('fecha_inicio', $vista === 'pendientes' ? 'asc' : 'desc')
            ->paginate(10)->withQueryString();

        $semana = $this->semanaNominal();

        return view('permisos.index', compact('solicitudes', 'vista', 'vistas', 'pendientes', 'semana'));
    }

    /**
     * Semana nominal que contiene hoy: miércoles → martes.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function semanaNominal(): array
    {
        $inicio = Carbon::now()->startOfWeek(Carbon::WEDNESDAY);

        return [$inicio, $inicio->copy()->addDays(6)->endOfDay()];
    }

    /**
     * Permisos con al menos un día dentro del rango (no solo el día de inicio).
     */
    private function traslapaCon($query, $desde, $hasta)
    {
        return $query->whereDate('fecha_inicio', '<=', $hasta)
                     ->whereDate('fecha_termino', '>=', $desde);
    }

    /**
     * Aplica el rango de fechas del filtro o, si no hay, la vista rápida elegida.
     * Devuelve la vista aplicada ('rango' cuando se filtró por fechas).
     */
    private function aplicarVistaOFechas($query, Request $request, array $vistas, string $porDefecto): string
    {
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $this->traslapaCon(
                $query,
                Carbon::parse($request->input('start_date'))->startOfDay(),
                Carbon::parse($request->input('end_date'))->endOfDay()
            );

            return 'rango';
        }

        $vista = array_key_exists($request->input('vista'), $vistas) ? $request->input('vista') : $porDefecto;
        [$inicio, $fin] = $this->semanaNominal();

        match ($vista) {
            'semana'            => $this->traslapaCon($query, $inicio, $fin),
            'pendientes'        => $query->where('estado', 'pendiente'),
            'semana_pendientes' => $query->where(fn ($q) => $q
                ->where('estado', 'pendiente')
                ->orWhere(fn ($w) => $this->traslapaCon($w, $inicio, $fin))),
            default             => null, // todas
        };

        return $vista;
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
    public function store(Request $request, PermisosService $permisos)
    {
        $datos = $request->validate(PermisosService::reglas() + [
            'departamento_id' => 'nullable|exists:departments,id',
        ]);

        $permisos->solicitar(auth()->user(), $datos, $request->file('archivo'));

        return redirect()->route('permisos.index')
            ->with('success', 'Solicitud enviada y notificada al supervisor.');
    }

    /**
     * Detalle de una solicitud.
     */
    public function show($id)
    {
        $solicitud = SolicitudPermiso::with('empleado', 'departamento')->findOrFail($id);
        Gate::authorize('view', $solicitud);

        return view('permisos.show', compact('solicitud'));
    }

    /**
     * Aprobar solicitud.
     */
    public function aprobar($id, PermisosService $permisos)
    {
        $permiso = SolicitudPermiso::findOrFail($id);
        Gate::authorize('approve', $permiso);

        $permisos->aprobar($permiso);

        return redirect()->route('permisos.index')->with('success', 'Permiso aprobado exitosamente.');
    }

    /**
     * Rechazar solicitud.
     */
    public function rechazar($id, PermisosService $permisos)
    {
        $permiso = SolicitudPermiso::findOrFail($id);
        Gate::authorize('reject', $permiso);

        $permisos->rechazar($permiso);

        return redirect()->route('permisos.index')->with('success', 'Permiso rechazado exitosamente.');
    }

    // ─── Vista RH ─────────────────────────────────────────────────────────────

    public function indexSolicitudesPermiso(Request $request)
    {
        $query = SolicitudPermiso::with('empleado', 'departamento')
            ->whereIn('estado', ['aprobado', 'rechazado', 'pendiente']);

        $pendientes = (clone $query)->where('estado', 'pendiente')->count();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('empleado', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        // RH conserva la semana nominal como vista por defecto (nómina)
        $vistas = ['semana' => 'Semana nominal', 'pendientes' => 'Pendientes', 'todas' => 'Todas'];
        $vista  = $this->aplicarVistaOFechas($query, $request, $vistas, 'semana');

        $permisos = $query->orderBy('fecha_inicio', $vista === 'pendientes' ? 'asc' : 'desc')
            ->paginate(25)->withQueryString();

        $semana = $this->semanaNominal();

        return view('solicitudes_permisos.index', compact('permisos', 'vista', 'vistas', 'pendientes', 'semana'));
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