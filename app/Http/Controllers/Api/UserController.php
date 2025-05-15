<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Retorna la foto de perfil del usuario solicitado
    public function fotoPerfil($id)
    {
        $user = User::findOrFail($id);

        if ($user->foto_perfil && \Storage::disk('public')->exists($user->foto_perfil)) {
            $url = asset('storage/' . $user->foto_perfil);
        } else {
            $url = null; // o una URL a una imagen por defecto si lo prefieres
        }

        return response()->json([
            'user_id' => $user->id,
            'foto_perfil_url' => $url,
        ]);
    }
}
