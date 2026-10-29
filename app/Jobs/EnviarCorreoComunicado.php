<?php

namespace App\Jobs;

use App\Mail\ComunicadoCorreo;
use App\Models\Comunicado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

/**
 * Manda un comunicado a un grupo de Microsoft 365, o a un bloque de hasta 50 personas en copia oculta.
 */
class EnviarCorreoComunicado implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** Segundos de espera entre intentos, si Graph falla */
    public array $backoff = [60, 300];

    public function __construct(
        public int $comunicadoId,
        public array $para,
        public array $ocultos = [],
    ) {
    }

    public function handle(): void
    {
        $comunicado = Comunicado::find($this->comunicadoId);

        // Si mientras esperaba en la cola lo archivaron o borraron, ya no se manda
        if (! $comunicado || $comunicado->estado !== 'publicado') {
            return;
        }

        $correo = Mail::to($this->para);

        if ($this->ocultos) {
            $correo->bcc($this->ocultos);
        }

        $correo->send(new ComunicadoCorreo($comunicado));
    }
}