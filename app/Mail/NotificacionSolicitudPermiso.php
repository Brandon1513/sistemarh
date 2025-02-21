<?php

namespace App\Mail;

use App\Models\SolicitudPermiso;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionSolicitudPermiso extends Mailable
{
    use Queueable, SerializesModels;

    public $permiso;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SolicitudPermiso $permiso)
    {
        $this->permiso = $permiso;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Control de Ausencias del Personal')
                    ->markdown('emails.notificacion_solicitud_permiso');
    }
}
