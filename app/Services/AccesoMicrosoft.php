<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Reglas para entrar con una cuenta de Microsoft: la usan la web y la app.
 */
class AccesoMicrosoft
{
    /**
     * Busca al empleado del correo que confirmó Microsoft.
     * Lanza un error de validación con un mensaje claro si no puede entrar.
     */
    public function usuarioPara(?string $correo): User
    {
        $correo = Str::lower(trim((string) $correo));

        if ($correo === '' || ! str_contains($correo, '@')) {
            $this->rechazar('Microsoft no nos compartió tu correo. Intenta de nuevo o entra con tu contraseña.');
        }

        // Solo cuentas de los dominios de Dasavena (evita invitados externos con correos personales)
        $dominio = Str::after($correo, '@');
        if (! in_array($dominio, config('services.azure.dominios', []), true)) {
            $this->rechazar('Solo las cuentas de Dasavena pueden entrar con Microsoft. Usa tu correo y contraseña.');
        }

        // El scope global "activo" del modelo deja fuera a los usuarios inactivos
        $usuario = User::whereRaw('LOWER(email) = ?', [$correo])->first();

        if (! $usuario) {
            $this->rechazar('Tu cuenta de Microsoft no está registrada en la intranet. Pide tu alta a Recursos Humanos.');
        }

        return $usuario;
    }

    private function rechazar(string $mensaje): never
    {
        throw ValidationException::withMessages(['email' => $mensaje]);
    }
}