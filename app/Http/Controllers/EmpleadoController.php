<?php

namespace App\Http\Controllers;
use Spatie\Permission\Models\Role;

use App\Models\User;
use Illuminate\View\View;
use App\Mail\NewUserCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class EmpleadoController extends Controller
{
    public function index()
    {
        // Obtener todos los usuarios registrados
        $users = User::all();
        
        // Retornar la vista del index con la lista de usuarios
        return view('empleados.index', compact('users'));
    }

    public function create(): View
    {
        // Filtrar usuarios que tengan el rol de 'jefe'
    $supervisors = User::whereHas('roles', function($query) {
        $query->where('name', 'jefe');
    })->get();

    // Obtener todos los roles disponibles
    $roles = Role::all(); // Obtener todos los roles

    // Retornar la vista con los supervisores y roles
    return view('empleados.create', [
        'supervisors' => $supervisors,
        'roles' => $roles, // Pasamos los roles a la vista
    ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'supervisor_id' => 'required|exists:users,id', 
    ]);

    // Crear el empleado
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'supervisor_id' => $request->supervisor_id,
    ]);

    // Obtener los roles seleccionados o asignar el rol 'empleado' por defecto
    $roles = Role::whereIn('name', $request->input('roles', ['empleado']))->get(); // Esto devuelve los objetos de los roles


    // Asignar los roles al usuario
    $user->syncRoles($roles);

    // Enviar el correo con las credenciales y roles asignados
    Mail::to($user->email)->send(new NewUserCreated($user, $request->password, $roles));

    return redirect()->route('empleados.index')->with('success', 'Empleado registrado exitosamente.');
}




    // Método para mostrar el formulario de edición de usuario
    public function edit($id): View
    {
        // Busca al usuario por su ID
        $user = User::findOrFail($id);

        // Filtra usuarios que tengan el rol 'jefe'
        $supervisors = User::whereHas('roles', function($query) {
            $query->where('name', 'jefe');
        })->get();

        // Retorna la vista de edición con la información del usuario
        return view('empleados.edit', compact('user', 'supervisors'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        'supervisor_id' => 'required|exists:users,id',
        'password' => 'nullable|string|min:8|confirmed', // La contraseña es opcional
    ]);

    $user = User::findOrFail($id);

    // Actualizar los campos
    $user->name = $request->name;
    $user->email = $request->email;
    $user->supervisor_id = $request->supervisor_id;

    // Solo actualizamos la contraseña si se proporciona una nueva
    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }

    $user->save();

    return redirect()->route('empleados.index')->with('success', 'Usuario actualizado correctamente.');
}
    public function destroy($id)
    {
        // Buscar el empleado por ID
        $empleado = User::findOrFail($id);

        // Eliminar el empleado
        $empleado->delete();

        // Redirigir con un mensaje de éxito
        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado exitosamente.');
    }
}
