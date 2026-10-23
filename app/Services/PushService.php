<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDeviceToken;
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

        $mensaje = CloudMessage::fromArray([
            'notification' => ['title' => $titulo, 'body' => $cuerpo],
            'data' => array_map('strval', $datos),
            'android' => [
                'priority' => 'high',
                'notification' => ['channel_id' => 'default'],
            ],
        ]);

        $reporte = $messaging->sendMulticast($mensaje, $tokens);

        // Tokens que ya no sirven (app desinstalada, sesión cerrada en ese teléfono)
        $invalidos = array_merge($reporte->invalidTokens(), $reporte->unknownTokens());

        if ($invalidos) {
            UserDeviceToken::whereIn('token', $invalidos)->delete();
        }
    }

    /** "Juan Pérez López" → "Juan" */
    public static function nombreCorto(?User $usuario): string
    {
        return $usuario ? explode(' ', trim($usuario->name))[0] : 'Un empleado';
    }
}
