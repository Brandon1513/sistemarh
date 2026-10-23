<?php

namespace App\Http\Controllers;

use App\Jobs\ExportLibroMayorVacacionesJob;
use App\Jobs\ExportWeekVacacionesJob;
use App\Models\Department;
use App\Models\PeriodoVacacion;
use App\Models\SolicitudVacacion;
use App\Services\VacacionesService;
use App\Support\CalendarioLaboral;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Sede;

class SolicitudVacacionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = SolicitudVacacion::with('empleado', 'departamento');

        if ($user->hasRole('jefe')) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('empleado', fn ($sq) => $sq->where('supervisor_id', $user->id))
                ->orWhere('empleado_id', $user->id);
            });
        } else {
            $query->where('empleado_id', $user->id);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('empleado', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('periodo')) {
            $query->where('periodo_correspondiente', (int) $request->input('periodo'));
        }

        $solicitudes = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('vacaciones.index', compact('solicitudes'));
    }
    public function create()
    {
        $user = auth()->user();

        $periodos = PeriodoVacacion::where('empleado_id', $user->id)
            ->where('activo', 1)
            ->get();

        $departments = Department::all();
        $departamentoNombre = $user->departamento ? $user->departamento->name : 'Sin departamento';

        // Festivos para bloquear y descontar días en el calendario del formulario
        $festivos = CalendarioLaboral::festivosEntre(now()->year, now()->year + 1, $user->sede_id);

        return view('vacaciones.create', compact('user', 'departments', 'departamentoNombre', 'periodos', 'festivos'));
    }

    public function show($id)
    {
        $vacationRequest = SolicitudVacacion::with('empleado', 'departamento')->findOrFail($id);
        Gate::authorize('view', $vacationRequest);

        return view('vacaciones.show', compact('vacationRequest'));
    }

    public function store(Request $request, VacacionesService $vacaciones)
    {
        $datos = $request->validate([
            'dias_solicitados'         => 'required|integer|min:1',
            'fecha_inicio_vacaciones'  => 'required|date',
            'fecha_termino_vacaciones' => 'required|date',
            'periodo_correspondiente'  => 'required|integer',
        ]);

        $vacaciones->solicitar(
            auth()->user(),
            (int) $datos['periodo_correspondiente'],
            $datos['fecha_inicio_vacaciones'],
            $datos['fecha_termino_vacaciones'],
            (int) $datos['dias_solicitados'],
        );

        return redirect()->route('vacaciones.index')
            ->with('success', 'Solicitud de vacaciones creada correctamente y notificación enviada al jefe directo.');
    }

    public function approve($id, VacacionesService $vacaciones)
    {
        $solicitud = SolicitudVacacion::findOrFail($id);
        Gate::authorize('approve', $solicitud);

        $vacaciones->aprobar($solicitud);

        return redirect()->route('vacaciones.index')->with('success', 'Solicitud aprobada y notificaciones enviadas.');
    }

    public function reject($id, VacacionesService $vacaciones)
    {
        $solicitud = SolicitudVacacion::findOrFail($id);
        Gate::authorize('reject', $solicitud);

        $vacaciones->rechazar($solicitud);

        return redirect()->route('vacaciones.index')->with('success', 'Solicitud rechazada y días devueltos correctamente.');
    }

    public function destroy($id, VacacionesService $vacaciones)
    {
        $solicitud = SolicitudVacacion::findOrFail($id);
        Gate::authorize('delete', $solicitud);

        $vacaciones->cancelar($solicitud);

        return redirect()->route('vacaciones.index')->with('success', 'Solicitud eliminada y días recuperados.');
    }

    // ─── Recursos Humanos ─────────────────────────────────────────────────────

    public function indexRH(Request $request)
    {
        $query = SolicitudVacacion::with(['empleado', 'departamento'])
            ->whereIn('estado', ['aprobado', 'rechazado', 'pendiente']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('empleado', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('fecha_inicio', [
                Carbon::parse($request->input('start_date'))->startOfDay(),
                Carbon::parse($request->input('end_date'))->endOfDay(),
            ]);
        }

        // Sin filtros: semana nominal (miércoles → martes)
        if (! $request->filled('search') && ! $request->filled('start_date') && ! $request->filled('end_date')) {
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::WEDNESDAY);
            $endOfWeek = $startOfWeek->copy()->addDays(6)->endOfDay();
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
    /**
 * Formulario de RH para registrar una vacación espontánea en nombre de un empleado.
 */
public function createEspontanea()
{
    $empleados = User::with(['departamento', 'sede'])
        ->whereNotNull('fecha_ingreso')
        ->orderBy('name')
        ->get();

    // Periodos con días disponibles, agrupados por empleado para el JavaScript de la vista
    $periodosPorEmpleado = PeriodoVacacion::whereIn('empleado_id', $empleados->pluck('id'))
        ->where('dias_disponibles', '>', 0)
        ->where('activo', 1)
        ->orderBy('anio', 'desc')
        ->get(['empleado_id', 'anio', 'dias_corresponden', 'dias_disponibles'])
        ->groupBy('empleado_id')
        ->map(fn ($periodos) => $periodos->map->only(['anio', 'dias_corresponden', 'dias_disponibles'])->values())
        ->toArray();

    // Festivos de cada sede, del año pasado al siguiente (se permiten fechas pasadas)
    $festivosPorSede = Sede::pluck('id')
        ->mapWithKeys(fn ($id) => [$id => CalendarioLaboral::festivosEntre(now()->year - 1, now()->year + 1, $id)])
        ->toArray();

    return view('solicitudes_vacaciones.create-espontanea', compact('empleados', 'periodosPorEmpleado', 'festivosPorSede'));
}

/**
 * Guarda la vacación espontánea registrada por RH.
 */
public function storeEspontanea(Request $request, VacacionesService $vacaciones)
{
    $datos = $request->validate([
        'empleado_id'                => 'required|exists:users,id',
        'dias_solicitados'           => 'required|integer|min:1',
        'fecha_inicio_vacaciones'    => 'required|date',
        'fecha_termino_vacaciones'   => 'required|date|after_or_equal:fecha_inicio_vacaciones',
        'periodo_correspondiente'    => 'required|integer',
        'fecha_presentarse_trabajar' => 'required|date',
    ]);

    $empleado = User::findOrFail($datos['empleado_id']);

    $vacaciones->registrarEspontanea(
        $empleado,
        auth()->user(),
        (int) $datos['periodo_correspondiente'],
        $datos['fecha_inicio_vacaciones'],
        $datos['fecha_termino_vacaciones'],
        (int) $datos['dias_solicitados'],
        $datos['fecha_presentarse_trabajar'],
    );

    return redirect()->route('solicitudes_vacaciones.index')
        ->with('success', "Vacación espontánea registrada para {$empleado->name}. El jefe directo recibirá notificación.");
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

    public function exportWeek()
    {
        ExportWeekVacacionesJob::dispatch(auth()->user()->email);

        return back()->with('success', 'La exportación de semana nominal está siendo procesada y será enviada por correo.');
    }

    /**
     * RH o administrador eliminan una solicitud pendiente o aprobada y devuelven los días.
     */
    public function destroyAdmin($id)
    {
        $vacation = SolicitudVacacion::with('empleado')->findOrFail($id);
        $nombreEmpleado = $vacation->empleado->name ?? 'N/A';

        DB::transaction(function () use ($vacation) {
            if (in_array($vacation->estado, ['aprobado', 'pendiente'])) {
                PeriodoVacacion::where('empleado_id', $vacation->empleado_id)
                    ->where('anio', $vacation->periodo_correspondiente)
                    ->lockForUpdate()
                    ->first()
                    ?->increment('dias_disponibles', $vacation->dias_solicitados);
            }

            $vacation->delete();
        });

        return redirect()
            ->route('solicitudes_vacaciones.index')
            ->with('success', "Solicitud de {$nombreEmpleado} eliminada correctamente. Los días fueron devueltos al período.");
    }
}