<?php


namespace App\Http\Controllers;

use id;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\PeriodoVacacion;

class PeriodoVacacionController extends Controller
{
    /**
     * Muestra la lista de periodos de vacaciones para el usuario autenticado.
     */
    public function index()
    {
        
        $periodos = PeriodoVacacion::select('anio')->distinct()->orderBy('anio', 'asc')->get();

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
    public function edit(PeriodoVacacion $periodo)
    {
        $user = auth()->user();

        // Verifica que el periodo pertenezca al usuario autenticado
        if ($periodo->user_id !== $user->id) {
            abort(403, 'No tienes permiso para editar esta solicitud.');
        }

        return view('vacaciones.edit', compact('periodo'));
    }

    /**
     * Actualiza una solicitud de vacaciones existente.
     */
    public function update(Request $request, PeriodoVacacion $periodo)
    {
        $request->validate([
            'dias_corresponden' => 'required|integer',
            'dias_solicitados' => 'required|integer',
            'pendientes_disfrutar' => 'required|integer',
            'periodo_correspondiente' => 'required|string',
            'fecha_inicio_vacaciones' => 'required|date',
            'fecha_termino_vacaciones' => 'required|date|after_or_equal:fecha_inicio_vacaciones',
            'fecha_presentarse_trabajar' => 'required|date|after:fecha_termino_vacaciones',
            'estado' => 'required|in:pendiente,aprobado,rechazado'
        ]);

        // Verifica que el periodo pertenezca al usuario autenticado
        if ($periodo->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para actualizar esta solicitud.');
        }

        $periodo->update($request->all());

        return redirect()->route('vacaciones.index')->with('success', 'Solicitud de vacaciones actualizada exitosamente.');
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
}
