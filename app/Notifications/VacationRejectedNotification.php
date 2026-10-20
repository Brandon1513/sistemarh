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
 
    public function via($notifiable) { return ['mail']; }
 
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('❌ Solicitud de Vacaciones Rechazada')
            ->view('emails.vacation_rejected', [
                'vacationRequest' => $this->vacationRequest,
            ]);
    }
}
