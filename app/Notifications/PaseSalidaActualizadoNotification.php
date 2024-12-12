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
        $mensaje = $this->estado === 'aprobado' 
            ? 'Tu pase de salida ha sido aprobado.' 
            : 'Tu pase de salida ha sido rechazado.';

        return (new MailMessage)
            ->subject('Actualización de Estado de Pase de Salida')
            ->line($mensaje)
            ->line('Contacta a tu jefe directo para obtener más información.')
            ->line('Gracias por utilizar nuestro sistema. Dasavena');
    }
}

