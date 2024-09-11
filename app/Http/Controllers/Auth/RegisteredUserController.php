<?php

namespace App\Http\Controllers\Auth;
use App\Http\Middleware\CheckAdminRole;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function __construct()
    {
        //$this->middleware('auth');
        //$this->middleware(\App\Http\Middleware\CheckAdminRole::class)->only('create', 'store');
    }

    public function create(): View
    {
        // Filtra usuarios que tengan el rol 'jefe'
        $supervisors = User::whereHas('roles', function($query) {
            $query->where('name', 'jefe');
        })->get();
        
        // Retorna la vista de registro con la lista de supervisores
        return view('auth.register', [
            'supervisors' => $supervisors,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'supervisor_id' => 'nullable|exists:users,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'supervisor_id' => $request->supervisor_id ?? null, // Aquí se guarda el supervisor_id, o null si no se selecciona
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
