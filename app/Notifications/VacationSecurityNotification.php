<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VacationSecurityNotification extends Notification
{
    protected $vacationRequest;

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
            ->subject('Solicitud de Vacaciones Aprobada')
            ->line('La solicitud de vacaciones para ' . $this->vacationRequest->empleado->name . ' ha sido aprobada.')
            ->line('El período de vacaciones es del ' . $this->vacationRequest->fecha_inicio . ' al ' . $this->vacationRequest->fecha_fin . '.')
            ->action('Ver Detalles', route('vacaciones.show', $this->vacationRequest->id))
            ->line('Por favor, permita al empleado salir de las instalaciones en las fechas indicadas.');
    } 
}
