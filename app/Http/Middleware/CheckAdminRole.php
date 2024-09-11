<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    public function handle($request, Closure $next)
    {
        // Verifica si el usuario está autenticado
        if (!Auth::check()) {
            dd('Usuario no autenticado');
            abort(403, 'Acceso denegado');
        }

        // Muestra la información del usuario autenticado
        $user = Auth::user();
        dd($user, $user->roles);

        // Verifica si el usuario tiene el rol de administrador
        if (!$user->hasRole('administrador')) {
            abort(403, 'Acceso denegado');
        }

        return $next($request);
    }
}
