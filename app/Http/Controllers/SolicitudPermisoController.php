<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\SolicitudPermiso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionSolicitudPermiso;
use Illuminate\Support\Facades\Log;

class SolicitudPermisoController extends Controller
{
    public function index()
    {
        // Obtener todas las solicitudes de permisos
        $solicitudes = SolicitudPermiso::with('empleado', 'departamento')->get();

        // Retornar la vista index con los datos de las solicitudes
        return view('permisos.index', compact('solicitudes'));
    }

    public function create()
    {
        // Obtener todos los departamentos
        $departamentos = Department::all();

        // Retornar la vista y pasar los departamentos
        return view('permisos.create', compact('departamentos'));
    }

   


    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'departamento_id' => 'required|exists:departments,id',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'required|string|max:255',
            'tipo_permiso' => 'required|string|in:Con Goce de Sueldo,Sin Goce de Sueldo',
            'fecha_regreso_laborar' => 'nullable|date|after_or_equal:fecha_termino',
            'tipo' => 'required|in:Permiso,Comisión,Suspensión',
            'dia_descanso' => 'nullable|string',
        ]);
    
        // Crear la solicitud de permiso
        $permiso = SolicitudPermiso::create([
            'empleado_id' => auth()->id(),
            'departamento_id' => $request->departamento_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_termino' => $request->fecha_termino,
            'motivo' => $request->motivo,
            'tipo_permiso' => $request->tipo_permiso,
            'estado' => 'pendiente',
            'fecha_regreso_laborar' => $request->fecha_regreso_laborar,
            'tipo' => $request->tipo,
            'dia_descanso' => $request->dia_descanso,
        ]);
    
        // Obtener al supervisor del empleado actual
        $supervisor = Auth::user()->supervisor;
    
        // Verificar que el supervisor existe y enviar el correo
        if ($supervisor && $supervisor->email) {
            Mail::to($supervisor->email)->send(new NotificacionSolicitudPermiso($permiso));
        }
    
        // Redirigir con mensaje de éxito
        return redirect()->route('permisos.index')->with('success', 'La solicitud de permiso ha sido enviada y notificada al supervisor.');
    }

    


    public function show($id)
    {
        // Buscar la solicitud de permiso por su ID
        $solicitud = SolicitudPermiso::with('empleado', 'departamento')->findOrFail($id);

        // Retornar la vista con la solicitud de permiso
        return view('permisos.show', compact('solicitud'));
    }

    public function aprobar($id)
    {
        // Buscar la solicitud de permiso por su ID
        $permiso = SolicitudPermiso::findOrFail($id);

        // Verificar si el permiso ya está aprobado o rechazado
        if ($permiso->estado != 'pendiente') {
            return redirect()->back()->with('error', 'El permiso ya ha sido procesado.');
        }

        // Cambiar el estado a "aprobado"
        $permiso->estado = 'aprobado';
        $permiso->save();

        // Redirigir con mensaje de éxito
        return redirect()->route('permisos.index')->with('success', 'Permiso aprobado con éxito.');
    }

    public function rechazar($id)
    {
        // Buscar la solicitud de permiso por su ID
        $permiso = SolicitudPermiso::findOrFail($id);

        // Verificar si el permiso ya está aprobado o rechazado
        if ($permiso->estado != 'pendiente') {
            return redirect()->back()->with('error', 'El permiso ya ha sido procesado.');
        }

        // Cambiar el estado a "rechazado"
        $permiso->estado = 'rechazado';
        $permiso->save();

        // Redirigir con mensaje de éxito
        return redirect()->route('permisos.index')->with('success', 'Permiso rechazado con éxito.');
    }
}
