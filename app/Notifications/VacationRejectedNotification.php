<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VacationRejectedNotification extends Notification
{
    use Queueable;

    public $vacationRequest;

    public function __construct($vacationRequest)
    {
        $this->vacationRequest = $vacationRequest;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Solicitud de Vacaciones Rechazada')
                    ->greeting('Hola, ' . $this->vacationRequest->empleado->name)
                    ->line('Lamentamos informarte que tu solicitud de vacaciones ha sido rechazada.')
                    ->line('Por favor, contacta con tu supervisor para más detalles.')
                    ->line('Gracias por utilizar nuestra aplicación.');
    }
}
