<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SupervisorController extends Controller
{
    public function index()
{
    $supervisors = User::role('jefe')->get(); // Obtiene todos los usuarios con el rol 'jefe'

    return view('supervisores.index', compact('supervisors'));
}

public function create()
{
    $supervisores = User::role('jefe')->get(); // Obtiene todos los usuarios con el rol 'jefe'
    
    return view('supervisores.create', compact('supervisores'));
}


public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'supervisor_id' => 'nullable|exists:users,id',
    ]);

    $supervisor = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'supervisor_id' => $validated['supervisor_id'],
    ]);

    // Asignar el rol de 'jefe' al usuario recién creado
    $supervisor->assignRole('jefe');

    return redirect()->route('supervisores.index')->with('success', 'Supervisor creado exitosamente.');
}


    public function edit(User $supervisore)
    {
        $supervisores = User::whereHas('roles', function($query) {
            $query->where('name', 'jefe');
        })->get();
    
        return view('supervisores.edit', compact('supervisore', 'supervisores'));
    }

    public function update(Request $request, User $supervisore)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $supervisore->id,
            'password' => 'nullable|string|min:8|confirmed',
            'supervisor_id' => 'nullable|exists:users,id',
        ]);

        $supervisore->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $supervisore->password,
            'supervisor_id' => $validated['supervisor_id'], // Aquí es donde se actualiza el supervisor_id
        ]);

        return redirect()->route('supervisores.index')->with('success', 'Supervisor actualizado exitosamente.');
    }

    public function destroy(User $supervisore)
{
    // Quitar todos los roles antes de eliminar el usuario
    $supervisore->removeRole('jefe'); // Elimina específicamente el rol de 'jefe'
    $supervisore->delete();

    return redirect()->route('supervisores.index')->with('success', 'Supervisor eliminado exitosamente.');
}

}
