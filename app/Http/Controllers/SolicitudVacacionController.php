<?php

namespace App\Http\Controllers;

use App\Models\SolicitudVacacion;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PeriodoVacacion;

class SolicitudVacacionController extends Controller
{
    public function index()
{
    // Obtener el usuario autenticado
    $user = auth()->user();
    
    // Obtener las solicitudes de vacaciones del usuario autenticado
    $solicitudes = SolicitudVacacion::where('empleado_id', $user->id)->get();

    return view('vacaciones.index', compact('solicitudes'));
}

    public function create()
{
    $user = auth()->user(); // Usuario autenticado
    $periodos = PeriodoVacacion::where('empleado_id', $user->id)->get(); // Filtra los periodos solo del usuario autenticado
    $departments = Department::all(); // Obtiene todos los departamentos
    

    // Verifica que el usuario tenga un departamento asociado
    if ($user->departamento) {
        $departamentoNombre = $user->departamento->name;
    } else {
        $departamentoNombre = 'Sin departamento'; // Valor por defecto si el departamento es nulo
    }

    return view('vacaciones.create', compact('user', 'departments', 'departamentoNombre','periodos'));
}
public function show($id)
{
    $solicitud = SolicitudVacacion::findOrFail($id);
    return view('vacaciones.show', compact('solicitud'));
}


public function store(Request $request)
{
    $user = auth()->user();

    // Validación de los datos
    $request->validate([
        'dias_solicitados' => 'required|integer|min:1',
        'fecha_inicio_vacaciones' => 'required|date',
        'fecha_termino_vacaciones' => 'required|date|after_or_equal:fecha_inicio_vacaciones',
        'periodo_correspondiente' => 'required|integer',
    ]);

    // Buscar el periodo seleccionado
    $periodo = PeriodoVacacion::where('empleado_id', $user->id)
                               ->where('anio', $request->periodo_correspondiente)
                               ->first();

    // Verificar si el empleado tiene suficientes días disponibles en el período seleccionado
    if (!$periodo || $periodo->dias_disponibles < $request->dias_solicitados) {
        return redirect()->back()->withErrors('No tienes suficientes días disponibles para este período.');
    }

    // Crear la solicitud de vacaciones y actualizar los días disponibles en el período
    $periodo->dias_disponibles -= $request->dias_solicitados;
    $periodo->save();

    SolicitudVacacion::create([
        'empleado_id' => $user->id,
        'departamento_id' => $user->departamento_id,
        'fecha_solicitud' => now(),
        'dias_corresponden' => $periodo->dias_corresponden,
        'dias_solicitados' => $request->dias_solicitados,
        'dias_pendientes' => $periodo->dias_disponibles,
        'periodo_correspondiente' => $periodo->anio,
        'fecha_inicio' => $request->fecha_inicio_vacaciones,
        'fecha_fin' => $request->fecha_termino_vacaciones,
        'fecha_reincorporacion' => Carbon::parse($request->fecha_termino_vacaciones)->addDay(),
        'estado' => 'pendiente',
    ]);

    return redirect()->route('vacaciones.index')->with('success', 'Solicitud de vacaciones creada correctamente.');
}


    
}
