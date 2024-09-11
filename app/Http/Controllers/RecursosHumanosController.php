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
        $recursosHumanos = User::whereHas('roles', function($query) {
            $query->where('name', 'recursos_humanos');
        })->get();

        return view('recursoshumanos.index', compact('recursosHumanos'));
    }

    public function create()
    {
        return view('recursoshumanos.create');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    // Asignar los roles 'recursos_humanos' y 'empleado'
    $user->assignRole('recursos_humanos', 'empleado');

    return redirect()->route('recursoshumanos.index');
}


    public function edit(User $recursoshumano)
    {
        return view('recursoshumanos.edit', compact('recursoshumano'));
    }

    public function update(Request $request, User $recursoshumano)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $recursoshumano->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $recursoshumano->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($request->password) {
            $recursoshumano->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('recursoshumanos.index');
    }

    public function destroy(User $recursoshumano)
    {
        $recursoshumano->delete();
        return redirect()->route('recursoshumanos.index');
    }
}


