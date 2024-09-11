<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Verifica si el usuario autenticado tiene el rol requerido
        if (!Auth::check() || !Auth::user()->hasRole($role)) {
            // Si no tiene el rol, redirige o lanza una excepción
            abort(403, 'Acceso denegado');
        }

        return $next($request);
    }
}
