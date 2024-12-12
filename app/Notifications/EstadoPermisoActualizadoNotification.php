<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EstadoPermisoActualizadoNotification extends Notification
{
    use Queueable;

    public $estado;

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
            ->subject('Estado de Permiso Actualizado')
            ->line('El estado de tu solicitud de permiso ha sido actualizado.')
            ->line('Nuevo estado: ' . ucfirst($this->estado))
            ->line('Contacta a tu jefe directo para obtener más información.')
            ->line('Gracias por usar nuestro sistema. Dasavena');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'estado' => $this->estado,
        ];
    }
}
