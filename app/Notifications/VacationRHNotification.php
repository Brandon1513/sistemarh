<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class VacationRHNotification extends Notification
{
    use Queueable;

    public $vacationRequest;
    public $status;

    public function __construct($vacationRequest, $status)
    {
        $this->vacationRequest = $vacationRequest;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mailMessage = new MailMessage;

        if ($this->status === 'aprobada') {
            $mailMessage->subject('Actualización de Solicitud de Vacaciones')
                        ->greeting('Hola Recursos Humanos,')
                        ->line('La solicitud de vacaciones del empleado ' . $this->vacationRequest->empleado->name . ' ha sido aprobada.')
                        ->line('Fecha de Inicio: ' . Carbon::parse($this->vacationRequest->fecha_inicio)->format('d/m/Y'))
                        ->line('Fecha de Fin: ' . Carbon::parse($this->vacationRequest->fecha_fin)->format('d/m/Y'))
                        ->line('Fecha de Reincorporación: ' . Carbon::parse($this->vacationRequest->fecha_reincorporacion)->format('d/m/Y'))
                        ->line('Gracias por utilizar nuestra aplicación.');
        } else {
            $mailMessage->subject('Actualización de Solicitud de Vacaciones')
                        ->greeting('Hola Recursos Humanos,')
                        ->line('La solicitud de vacaciones del empleado ' . $this->vacationRequest->empleado->name . ' ha sido rechazada.')
                        ->line('Gracias por utilizar nuestra aplicación.');
        }

        return $mailMessage;
    }
}
