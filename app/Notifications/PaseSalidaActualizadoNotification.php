<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaseSalidaActualizadoNotification extends Notification
{
    use Queueable;

    protected $estado;

    /**
     * Create a new notification instance.
     */
    public function __construct($estado)
    {
        $this->estado = $estado;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🚪 Actualización de Estado de Pase de Salida')
            ->view('emails.pase_salida_actualizado', [
                'estado' => $this->estado,
            ]);
    }
}

