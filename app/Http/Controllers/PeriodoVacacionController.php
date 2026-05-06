<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PeriodoVacacion;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PeriodosVacacionesExport;
use Illuminate\Support\Facades\Log;

class PeriodoVacacionController extends Controller
{
    /**
     * Muestra la lista de periodos de vacaciones.
     */
    public function index(Request $request)
    {
        $query = PeriodoVacacion::with('empleado');

        // Filtro por nombre o clave del empleado
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('empleado', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('clave_empleado', 'like', "%{$search}%");
            });
        }

        // Filtro por año
        if ($request->filled('anio')) {
            $query->where('anio', $request->input('anio'));
        }

        // Filtro por estado activo/inactivo
        if ($request->filled('estado')) {
            $query->where('activo', $request->input('estado'));
        }

        $periodos = $query->orderBy('anio', 'desc')->paginate(10)->withQueryString();

        return view('periodos.index', compact('periodos'));
    }

    /**
     * Formulario para crear un nuevo período.
     */
    public function create()
    {
        $usuarios = User::whereNotNull('fecha_ingreso')->orderBy('name')->get();
        return view('periodos.create', compact('usuarios'));
    }

    /**
     * Guarda los períodos para los empleados seleccionados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'empleado_id'   => 'required|array|min:1',
            'empleado_id.*' => 'exists:users,id',
            'anio'          => 'required|integer|min:2000|max:2100',
        ]);

        foreach ($request->empleado_id as $empleadoId) {
            // Evitar duplicados: si ya existe el período para ese empleado y año, lo omitimos
            $existe = PeriodoVacacion::where('empleado_id', $empleadoId)
                ->where('anio', $request->anio)
                ->exists();

            if ($existe) continue;

            $diasCorresponden = $this->calcularDiasCorrespondenSegunAntiguedad($empleadoId, $request->anio);

            PeriodoVacacion::create([
                'empleado_id'       => $empleadoId,
                'anio'              => $request->anio,
                'dias_corresponden' => $diasCorresponden,
                'dias_disponibles'  => $diasCorresponden,
                'activo'            => 1,
            ]);
        }

        return redirect()->route('periodos.index')->with('success', 'Períodos de vacaciones guardados exitosamente.');
    }

    /**
     * Formulario de edición de un período.
     */
    public function edit($id)
    {
        $user    = auth()->user();
        $periodo = PeriodoVacacion::with('empleado')->findOrFail($id);

        if ($periodo->empleado_id !== $user->id && !$user->hasAnyRole(['administrador', 'recursos_humanos'])) {
            abort(403, 'No tienes permiso para editar esta solicitud.');
        }

        return view('periodos.edit', compact('periodo'));
    }

    /**
     * Actualiza los días disponibles de un período.
     */
    public function update(Request $request, $id)
    {
        $periodo = PeriodoVacacion::findOrFail($id);

        $request->validate([
            'dias_disponibles' => 'required|integer|min:0|max:' . $periodo->dias_corresponden,
        ]);

        $periodo->dias_disponibles = $request->input('dias_disponibles');
        $periodo->save();

        return redirect()->route('periodos.index')->with('success', 'El período ha sido actualizado correctamente.');
    }

    /**
     * Elimina un período.
     */
    public function destroy(PeriodoVacacion $periodo)
    {
        if ($periodo->empleado_id !== auth()->id()) {
            abort(403, 'No tienes permiso para eliminar esta solicitud.');
        }

        $periodo->delete();

        return redirect()->route('periodos.index')->with('success', 'Período eliminado exitosamente.');
    }

    /**
     * Calcula los días que corresponden a cada empleado seleccionado (llamado via fetch/AJAX).
     */
    public function calculateDays(Request $request)
    {
        $empleadoIds = $request->input('empleado_ids', []);
        $anio        = $request->input('anio');
        $response    = [];

        if (empty($empleadoIds) || !$anio) {
            return response()->json([]);
        }

        foreach ($empleadoIds as $empleadoId) {
            $empleado = User::find($empleadoId);

            // Si el empleado no existe o no tiene fecha de ingreso, lo saltamos
            if (!$empleado || !$empleado->fecha_ingreso) continue;

            $fechaIngreso = Carbon::parse($empleado->fecha_ingreso);
            $antiguedad   = (int) $anio - (int) $fechaIngreso->year;

            // Antigüedad mínima de 1 año para tener días
            if ($antiguedad < 1) {
                $response[] = [
                    'empleado'        => $empleado->name,
                    'dias_corresponden' => 0,
                    'dias_disponibles'  => 0,
                    'nota'            => 'Menos de 1 año de antigüedad en ' . $anio,
                ];
                continue;
            }

            $dias = $this->calcularDiasCorresponden($antiguedad);

            $response[] = [
                'empleado'          => $empleado->name,
                'dias_corresponden' => $dias,
                'dias_disponibles'  => $dias,
            ];
        }

        return response()->json($response);
    }

    /**
     * Activa o inactiva un período.
     */
    public function toggleActivo($id)
    {
        $periodo         = PeriodoVacacion::findOrFail($id);
        $periodo->activo = !$periodo->activo;
        $periodo->save();

        return redirect()->back()->with('success', 'Estado del período actualizado correctamente.');
    }

    /**
     * Exporta a Excel.
     */
    public function export()
    {
        return Excel::download(new PeriodosVacacionesExport, 'periodos-vacaciones.xlsx');
    }

    /**
     * Job/Comando: activa períodos automáticamente en el aniversario del empleado.
     */
    public function activarPeriodosAutomáticamente()
    {
        $hoy      = Carbon::now();
        $empleados = User::whereNotNull('fecha_ingreso')->get();

        Log::info("Revisión automática de períodos — {$hoy->toDateString()} — {$empleados->count()} empleados.");

        foreach ($empleados as $empleado) {
            $fechaIngreso = Carbon::parse($empleado->fecha_ingreso);

            if ($fechaIngreso->day !== $hoy->day || $fechaIngreso->month !== $hoy->month) continue;

            Log::info("Empleado {$empleado->id} cumple aniversario hoy.");

            $anioActual = $hoy->year;
            $periodo    = PeriodoVacacion::where('empleado_id', $empleado->id)
                ->where('anio', $anioActual)
                ->first();

            if ($periodo) {
                if (!$periodo->activo) {
                    $periodo->activo = 1;
                    $periodo->save();
                    Log::info("Período {$anioActual} ACTIVADO para empleado {$empleado->id}.");
                }
            } else {
                $antiguedad          = $anioActual - $fechaIngreso->year;
                $diasCorrespondientes = $this->calcularDiasCorresponden($antiguedad);

                PeriodoVacacion::create([
                    'empleado_id'       => $empleado->id,
                    'anio'              => $anioActual,
                    'dias_corresponden' => $diasCorrespondientes,
                    'dias_disponibles'  => $diasCorrespondientes,
                    'activo'            => 1,
                ]);
                Log::info("Nuevo período {$anioActual} CREADO para empleado {$empleado->id}.");
            }
        }

        return response()->json(['message' => 'Revisión y activación de períodos completada.']);
    }

    // ─── Helpers privados ────────────────────────────────────────────────────

    private function calcularDiasCorrespondenSegunAntiguedad($empleadoId, $anio)
    {
        $empleado = User::find($empleadoId);
        if (!$empleado || !$empleado->fecha_ingreso) return 0;

        $antiguedad = (int) $anio - (int) Carbon::parse($empleado->fecha_ingreso)->year;
        return $this->calcularDiasCorresponden($antiguedad);
    }

    private function calcularDiasCorresponden($antiguedad)
    {
        if ($antiguedad <= 0)  return 0;
        if ($antiguedad == 1)  return 12;
        if ($antiguedad == 2)  return 14;
        if ($antiguedad == 3)  return 16;
        if ($antiguedad == 4)  return 18;
        if ($antiguedad == 5)  return 20;
        if ($antiguedad <= 10) return 22;
        if ($antiguedad <= 15) return 24;
        if ($antiguedad <= 20) return 26;
        if ($antiguedad <= 25) return 28;
        if ($antiguedad <= 30) return 30;
        if ($antiguedad <= 35) return 32;
        return 32;
    }
}