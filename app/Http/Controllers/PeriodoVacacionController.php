<?php


namespace App\Http\Controllers;

use id;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\PeriodoVacacion;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PeriodosVacacionesExport;
use Illuminate\Support\Facades\Log;

class PeriodoVacacionController extends Controller
{
    /**
     * Muestra la lista de periodos de vacaciones para el usuario autenticado.
     */
    public function index(Request $request)
    {
        // Iniciar una consulta sobre los periodos de vacaciones
        $query = PeriodoVacacion::query();
    
        // Filtro de búsqueda por nombre o clave del empleado
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('empleado', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('clave_empleado', 'like', "%{$search}%");
            });
        }
    
        // Obtener los periodos de vacaciones con el empleado relacionado y aplicar paginación
        $periodos = $query->with('empleado')->orderBy('anio', 'asc')->paginate(10);
    
        return view('periodos.index', compact('periodos'));
    }
    


    /**
     * Muestra el formulario para crear una nueva solicitud de vacaciones.
     */
    public function create()
{
    $usuarios = User::all(); // Obtener todos los usuarios para asignarles periodos de vacaciones
    return view('periodos.create', compact('usuarios'));
}

    /**
     * Almacena una nueva solicitud de vacaciones.
     */
    public function store(Request $request)
{
    $request->validate([
        'empleado_id' => 'required|array|min:1',
        'anio' => 'required|integer',
    ]);

    // Itera sobre cada empleado seleccionado y guarda el periodo de vacaciones
    foreach ($request->empleado_id as $empleadoId) {
        $diasCorresponden = $this->calcularDiasCorrespondenSegunAntiguedad($empleadoId, $request->anio); // Supongamos que tienes una función para calcular los días

        PeriodoVacacion::create([
            'empleado_id' => $empleadoId,
            'anio' => $request->anio,
            'dias_corresponden' => $diasCorresponden,
            'dias_disponibles' => $diasCorresponden, // Inicia los días disponibles con el total de días correspondientes
        ]);
    }

    return redirect()->route('periodos.index')->with('success', 'Periodos de vacaciones guardados exitosamente.');
}

// Función para calcular los días que corresponden según la antigüedad
private function calcularDiasCorrespondenSegunAntiguedad($empleadoId, $anio)
{
    // Lógica para calcular días en función de la antigüedad del empleado
    $empleado = User::find($empleadoId);
    $fechaIngreso = Carbon::parse($empleado->fecha_ingreso);
    $antiguedad = $anio - $fechaIngreso->year;

    return $this->calcularDiasCorresponden($antiguedad); // Usa la lógica de tu función para calcular días
}



    /**
     * Muestra el formulario para editar una solicitud de vacaciones existente.
     */
    public function edit($id)
    {
        $user = auth()->user();
        $periodo = PeriodoVacacion::findOrFail($id);
    
        // Permite la edición si el usuario es el propietario del periodo o si tiene los roles necesarios
        if ($periodo->user_id !== $user->id && !$user->hasAnyRole(['administrador', 'recursos_humanos'])) {
            abort(403, 'No tienes permiso para editar esta solicitud.');
        }

        //dd($periodo);
    
        return view('periodos.edit', compact('periodo'));
    }
    


    /**
     * Actualiza una solicitud de vacaciones existente.
     */
    public function update(Request $request, $id)
{
    $periodo = PeriodoVacacion::find($id);
    if (!$periodo) {
        return redirect()->route('periodos.index')->with('error', 'El periodo no fue encontrado.');
    }

    $request->validate([
        'dias_disponibles' => 'required|integer',
    ]);

    $periodo->dias_disponibles = $request->input('dias_disponibles');
    $periodo->save();

    return redirect()->route('periodos.index')->with('success', 'El periodo ha sido actualizado correctamente.');
}



    
    

    /**
     * Elimina una solicitud de vacaciones.
     */
    public function destroy(PeriodoVacacion $periodo)
    {
        // Verifica que el periodo pertenezca al usuario autenticado
        if ($periodo->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para eliminar esta solicitud.');
        }

        $periodo->delete();

        return redirect()->route('vacaciones.index')->with('success', 'Solicitud de vacaciones eliminada exitosamente.');
    }

    public function calculateDays(Request $request)
    {
        $empleadoIds = $request->empleado_ids;
        $anio = $request->anio;
        $response = [];
    
        foreach ($empleadoIds as $empleadoId) {
            $empleado = User::find($empleadoId);
            $fechaIngreso = Carbon::parse($empleado->fecha_ingreso);
            $antiguedad = $anio - $fechaIngreso->year;
    
            $diasCorrespondentes = $this->calcularDiasCorresponden($antiguedad);
    
            $response[] = [
                'empleado' => $empleado->name,
                'dias_corresponden' => $diasCorrespondentes,
                'dias_disponibles' => $diasCorrespondentes
            ];
        }
    
        return response()->json($response);
    }
    

private function calcularDiasCorresponden($antiguedad)
    {
        if ($antiguedad == 1) return 12;
        elseif ($antiguedad == 2) return 14;
        elseif ($antiguedad == 3) return 16;
        elseif ($antiguedad == 4) return 18;
        elseif ($antiguedad == 5) return 20;
        elseif ($antiguedad >= 6 && $antiguedad <= 10) return 22;
        elseif ($antiguedad >= 11 && $antiguedad <= 15) return 24;
        elseif ($antiguedad >= 16 && $antiguedad <= 20) return 26;
        elseif ($antiguedad >= 21 && $antiguedad <= 25) return 28;
        elseif ($antiguedad >= 26 && $antiguedad <= 30) return 30;
        elseif ($antiguedad >= 31 && $antiguedad <= 35) return 32;
        else return 32;
    }

    public function toggleActivo($id)
{
    $periodo = PeriodoVacacion::findOrFail($id);
    $periodo->activo = !$periodo->activo; // Cambia el estado de activo
    $periodo->save();

    return redirect()->back()->with('success', 'Estado del período actualizado correctamente.');
}

    public function export()
{
    return Excel::download(new PeriodosVacacionesExport, 'periodos-vacaciones.xlsx');
}

public function activarPeriodosAutomáticamente()
{
    $hoy = Carbon::now();
    Log::info("Hoy es: " . $hoy->toDateString());

    // Obtener todos los empleados con fecha_ingreso
    $empleados = User::whereNotNull('fecha_ingreso')->get();
    Log::info("Empleados con fecha_ingreso encontrados: " . $empleados->count());

    foreach ($empleados as $empleado) {
        $fechaIngreso = Carbon::parse($empleado->fecha_ingreso);
        Log::info("Empleado ID={$empleado->id}, Fecha de Ingreso: {$fechaIngreso->toDateString()}");

        // Comparar solo día y mes
        if ($fechaIngreso->day == $hoy->day && $fechaIngreso->month == $hoy->month) {
            Log::info("El empleado {$empleado->id} cumple aniversario hoy.");

            $anioActual = $hoy->year;
            $periodo = PeriodoVacacion::where('empleado_id', $empleado->id)
                ->where('anio', $anioActual)
                ->first();

            if ($periodo) {
                if (!$periodo->activo) {
                    $periodo->activo = 1;
                    $periodo->save();
                    Log::info("Periodo {$anioActual} ACTIVADO para el empleado {$empleado->id}.");
                } else {
                    Log::info("Periodo {$anioActual} ya estaba activo para el empleado {$empleado->id}.");
                }
            } else {
                $antiguedad = $anioActual - $fechaIngreso->year;
                $diasCorrespondientes = $this->calcularDiasCorresponden($antiguedad);

                PeriodoVacacion::create([
                    'empleado_id' => $empleado->id,
                    'anio' => $anioActual,
                    'dias_corresponden' => $diasCorrespondientes,
                    'dias_disponibles' => $diasCorrespondientes,
                    'activo' => 1,
                ]);
                Log::info("Nuevo periodo {$anioActual} CREADO y ACTIVADO para el empleado {$empleado->id}.");
            }
        } else {
            Log::info("El empleado {$empleado->id} NO cumple aniversario hoy.");
        }
    }

    return response()->json(['message' => 'Revisión y activación de periodos completada.']);
}

}