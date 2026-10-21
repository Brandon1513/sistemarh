<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UsuarioResource;
use App\Models\User;
use App\Models\UserDeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $datos = $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string',
            'dispositivo' => 'nullable|string|max:100',
        ]);

        // El global scope de "activo" hace que los usuarios dados de baja no se encuentren
        $user = User::where('email', strtolower(trim($datos['email'])))->first();

        if (! $user || ! Hash::check($datos['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'El correo o la contraseña no son correctos.',
            ]);
        }

        $token = $user->createToken($datos['dispositivo'] ?? 'app-movil')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => new UsuarioResource($user->load('departamento', 'supervisor')),
        ]);
    }

   public function logout(Request $request)
    {
        $request->validate([
            'token_push' => 'nullable|string|max:255',
        ]);

        // Si la app manda el token del teléfono, deja de recibir notificaciones de este usuario
        if ($request->filled('token_push')) {
            UserDeviceToken::where('token', $request->input('token_push'))
                ->where('user_id', $request->user()->id)
                ->delete();
        }

        // Borra solo el token de sesión de este teléfono; las demás sesiones siguen activas
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    public function me(Request $request)
    {
        return new UsuarioResource($request->user()->load('departamento', 'supervisor'));
    }
}