<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDeviceToken;
use Illuminate\Database\Eloquent\Builder;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

class PushService
{
    /**
     * Manda una notificación a todos los teléfonos registrados de un usuario.
     * $datos viaja con la notificación; la app usa 'destino' para saber qué pantalla abrir.
     */
    public function enviar(User $usuario, string $titulo, string $cuerpo, array $datos = []): void
    {
        $tokens = $usuario->dispositivos()->pluck('token')->all();

        if (! $tokens) {
            return;
        }

        // Se pide aquí y no en el constructor: si Firebase falla, solo falla la push,
        // no la solicitud que la generó.
        $messaging = app(Messaging::class);

        $reporte = $messaging->sendMulticast($this->mensaje($titulo, $cuerpo, $datos), $tokens);

        $this->limpiarTokensInvalidos($reporte);
    }

    /**
     * Manda la misma notificación a muchos usuarios (los destinatarios de un comunicado).
     * Firebase acepta hasta 500 teléfonos por petición, así que se manda en bloques.
     * Regresa a cuántos teléfonos llegó.
     */
    public function enviarAMuchos(Builder $usuarios, string $titulo, string $cuerpo, array $datos = []): int
    {
        $tokens = UserDeviceToken::whereIn('user_id', $usuarios->select('users.id'))
            ->pluck('token')
            ->unique()
            ->values();

        if ($tokens->isEmpty()) {
            return 0;
        }

        $messaging = app(Messaging::class);
        $mensaje = $this->mensaje($titulo, $cuerpo, $datos);

        $enviados = 0;
        foreach ($tokens->chunk(500) as $bloque) {
            $reporte = $messaging->sendMulticast($mensaje, $bloque->values()->all());
            $enviados += $reporte->successes()->count();

            $this->limpiarTokensInvalidos($reporte);
        }

        return $enviados;
    }

    /** "Juan Pérez López" → "Juan" */
    public static function nombreCorto(?User $usuario): string
    {
        return $usuario ? explode(' ', trim($usuario->name))[0] : 'Un empleado';
    }

    /**
     * El mensaje con el formato que usa la app: prioridad alta y el canal "default"
     * (para que en Android aparezca arriba de la pantalla y no solo en la barra).
     */
    private function mensaje(string $titulo, string $cuerpo, array $datos): CloudMessage
    {
        return CloudMessage::fromArray([
            'notification' => ['title' => $titulo, 'body' => $cuerpo],
            'data'         => array_map('strval', $datos),
            'android'      => [
                'priority'     => 'high',
                'notification' => ['channel_id' => 'default'],
            ],
        ]);
    }

    /**
     * Tokens que ya no sirven (app desinstalada, sesión cerrada en ese teléfono): se borran
     * para que la tabla no se llene de teléfonos que ya no existen.
     */
    private function limpiarTokensInvalidos($reporte): void
    {
        $invalidos = array_merge($reporte->invalidTokens(), $reporte->unknownTokens());

        if ($invalidos) {
            UserDeviceToken::whereIn('token', $invalidos)->delete();
        }
    }
}