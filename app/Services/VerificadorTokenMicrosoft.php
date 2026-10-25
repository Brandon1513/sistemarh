<?php

namespace App\Services;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

/**
 * Verifica el id_token que la app obtiene de Microsoft y devuelve el correo de la persona.
 */
class VerificadorTokenMicrosoft
{
    public function correoDe(string $idToken): ?string
    {
        $tenant = (string) config('services.azure.tenant');
        $clientId = (string) config('services.azure.client_id');

        // Tolerancia para pequeñas diferencias de reloj entre el servidor y Microsoft
        JWT::$leeway = 60;

        try {
            $datos = JWT::decode($idToken, $this->llaves($tenant));
        } catch (\Throwable $e) {
            // Microsoft rota sus llaves de vez en cuando: si falla, se intenta una vez con llaves frescas
            try {
                $datos = JWT::decode($idToken, $this->llaves($tenant, refrescar: true));
            } catch (\Throwable) {
                $this->rechazar();
            }
        }

        // El token debe ser para NUESTRA aplicación y de NUESTRO Microsoft 365
        if (($datos->aud ?? null) !== $clientId || ($datos->tid ?? null) !== $tenant) {
            $this->rechazar();
        }

        if (($datos->iss ?? null) !== "https://login.microsoftonline.com/{$tenant}/v2.0") {
            $this->rechazar();
        }

        return $datos->email ?? $datos->preferred_username ?? null;
    }

    /**
     * Las llaves públicas con las que Microsoft firma los tokens. Se guardan 12 horas.
     */
    private function llaves(string $tenant, bool $refrescar = false): array
    {
        $clave = "microsoft_jwks_{$tenant}";

        if ($refrescar) {
            Cache::forget($clave);
        }

        $jwks = Cache::remember($clave, now()->addHours(12), fn () => Http::timeout(10)
            ->get("https://login.microsoftonline.com/{$tenant}/discovery/v2.0/keys")
            ->throw()
            ->json());

        // Las llaves de Microsoft no traen "alg"; todas son RS256
        return JWK::parseKeySet($jwks, 'RS256');
    }

    private function rechazar(): never
    {
        throw ValidationException::withMessages([
            'id_token' => 'No pudimos validar tu cuenta de Microsoft. Intenta de nuevo.',
        ]);
    }
}