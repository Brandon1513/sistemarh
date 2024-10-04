<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RecursosHumanosController extends Controller
{
    public function index()
    {
        $recursosHumanos = User::role('recursos_humanos')->get(); // Usuarios con el rol 'recursos_humanos'
        return view('recursoshumanos.index', compact('recursosHumanos'));
    }

    public function create()
    {
    $supervisores = User::role('jefe')->get(); // Asumiendo que el rol de supervisor es 'jefe'
    return view('recursoshumanos.create', compact('supervisores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'supervisor_id' => 'nullable|exists:users,id', // Validar que el supervisor sea válido o nulo
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'supervisor_id' => $validated['supervisor_id'], // Guardar el supervisor_id
        ]);

        // Asignar los roles 'recursos_humanos' y 'empleado'
        $user->assignRole('recursos_humanos', 'empleado');

        return redirect()->route('recursoshumanos.index')->with('success', 'Recurso humano creado exitosamente.');
    }

    public function edit(User $recursoshumano)
    {
        $supervisores = User::role('jefe')->get();
        return view('recursoshumanos.edit', compact('recursoshumano','supervisores'));
    }

    public function update(Request $request, User $recursoshumano)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $recursoshumano->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'supervisor_id' => 'nullable|exists:users,id', // Validar el supervisor
        ]);

        $recursoshumano->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'supervisor_id' => $validated['supervisor_id'], // Actualizar el supervisor
        ]);

        if ($request->password) {
            $recursoshumano->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('recursoshumanos.index')->with('success', 'Recurso humano actualizado exitosamente.');
    }

    public function destroy(User $recursoshumano)
    {
        $recursoshumano->removeRole('recursos_humanos'); // Elimina específicamente el rol de 'recursos_humanos'
        $recursoshumano->delete();
        return redirect()->route('recursoshumanos.index')->with('success', 'Recurso humano eliminado exitosamente.');
    }
}
;