<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use App\Models\Department;
use App\Mail\NewUserCreated;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EmpleadoController extends Controller
{
    private function getCommonData()
    {
        $supervisors = User::whereHas('roles', function ($query) {
            $query->where('name', 'jefe');
        })->get();

        $roles = Role::all();
        $departamentos = Department::all();

        return compact('supervisors', 'roles', 'departamentos');
    }

    public function index(Request $request)
    {
        $query = User::withoutGlobalScope('activo');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10);
        return view('empleados.index', compact('users'));
    }

    public function toggle($id)
    {
        $user = User::withoutGlobalScope('activo')->findOrFail($id);
        $user->activo = !$user->activo;
        $user->save();

        $message = $user->activo ? 'Usuario activado exitosamente.' : 'Usuario inactivado exitosamente.';
        return redirect()->route('empleados.index')->with('success', $message);
    }

    public function create(): View
    {
        return view('empleados.create', $this->getCommonData());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'supervisor_id' => 'required|exists:users,id',
            'clave_empleado' => 'nullable|string|max:255',
            'fecha_ingreso' => 'nullable|date',
            'puesto_empleado' => 'nullable|string|max:255',
            'departamento_id' => 'required|exists:departments,id',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'clave_empleado' => $request->clave_empleado,
            'fecha_ingreso' => $request->fecha_ingreso,
            'puesto_empleado' => $request->puesto_empleado,
            'supervisor_id' => $request->supervisor_id,
            'departamento_id' => $request->departamento_id,
        ]);

        if ($request->hasFile('foto_perfil')) {
            $image = $request->file('foto_perfil');
            $imagePath = $image->store('fotos_perfil', 'public');
            $user->foto_perfil = $imagePath;
            $user->save();
        }

        $roles = Role::whereIn('name', $request->input('roles', ['empleado']))->get();
        $user->syncRoles($roles);

        Mail::to($user->email)->queue(new NewUserCreated($user, $request->password, $roles));

        return redirect()->route('empleados.index')->with('success', 'Empleado registrado exitosamente.');
    }

    public function edit($id): View
    {
        $user = User::findOrFail($id);
        $data = array_merge(['user' => $user], $this->getCommonData());

        return view('empleados.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'supervisor_id' => 'required|exists:users,id',
            'password' => 'nullable|string|min:8|confirmed',
            'clave_empleado' => 'nullable|string|max:255',
            'fecha_ingreso' => 'nullable|date',
            'puesto_empleado' => 'nullable|string|max:255',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->supervisor_id = $request->supervisor_id;
        $user->clave_empleado = $request->clave_empleado;
        $user->fecha_ingreso = $request->fecha_ingreso;
        $user->puesto_empleado = $request->puesto_empleado;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($request->hasFile('foto_perfil')) {
            if ($user->foto_perfil && \Storage::disk('public')->exists($user->foto_perfil)) {
                \Storage::disk('public')->delete($user->foto_perfil);
            }

            $image = $request->file('foto_perfil');
            $imagePath = $image->store('fotos_perfil', 'public');
            $user->foto_perfil = $imagePath;
            $user->save();
        }

        if ($request->has('roles')) {
            $user->syncRoles($request->input('roles'));
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('empleados.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->activo = false;
        $user->save();

        return redirect()->route('empleados.index')->with('success', 'Empleado inactivo correctamente.');
    }
}
