<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserDeviceToken;
use Illuminate\Http\Request;

class DispositivoController extends Controller
{
    /**
     * Registra el token de notificaciones del teléfono para el usuario con sesión.
     * Si el token ya existía (otra persona usó este teléfono), pasa al usuario actual.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'token' => 'required|string|max:255',
        ]);

        UserDeviceToken::updateOrCreate(
            ['token' => $datos['token']],
            ['user_id' => $request->user()->id]
        );

        return response()->json(['message' => 'Dispositivo registrado.']);
    }
}