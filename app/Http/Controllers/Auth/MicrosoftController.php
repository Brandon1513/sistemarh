<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AccesoMicrosoft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftController extends Controller
{
    public function redirigir()
    {
        return Socialite::driver('azure')
            // Si la persona tiene varias cuentas abiertas en el navegador, que elija la de Dasavena
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function callback(Request $request, AccesoMicrosoft $acceso)
    {
        // El usuario canceló o Microsoft devolvió un error
        if ($request->has('error')) {
            return redirect()->route('login')->withErrors(['email' => 'No se completó el inicio de sesión con Microsoft.']);
        }

        try {
            $cuenta = Socialite::driver('azure')->user();
        } catch (\Throwable $e) {
            Log::warning('Inicio de sesión con Microsoft falló', ['error' => $e->getMessage()]);

            return redirect()->route('login')->withErrors(['email' => 'No pudimos validar tu cuenta de Microsoft. Intenta de nuevo.']);
        }

        // Microsoft manda el correo en "mail"; si no lo tiene, el usuario principal (UPN) lo sustituye
        $correo = $cuenta->getEmail() ?: ($cuenta->user['userPrincipalName'] ?? null);

        try {
            $usuario = $acceso->usuarioPara($correo);
        } catch (ValidationException $e) {
            return redirect()->route('login')->withErrors($e->errors());
        }

        Auth::login($usuario, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}