<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class VacationApprovedNotification extends Notification
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
                ->subject('Solicitud de Vacaciones Aprobada')
                ->greeting('Hola, ' . $this->vacationRequest->empleado->name)
                ->line('Tu solicitud de vacaciones ha sido aprobada.')
                ->line('Fecha de Inicio: ' . Carbon::parse($this->vacationRequest->fecha_inicio)->format('d/m/Y'))
                ->line('Fecha de Fin: ' . Carbon::parse($this->vacationRequest->fecha_fin)->format('d/m/Y'))
                ->line('Fecha de Reincorporación: ' . Carbon::parse($this->vacationRequest->fecha_reincorporacion)->format('d/m/Y'))
                ->line('Gracias por utilizar nuestra aplicación.');
}
}
