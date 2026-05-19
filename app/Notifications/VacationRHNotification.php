<?php

namespace App\Notifications;
 
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
 
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
 
    public function via($notifiable) { return ['mail']; }
 
    public function toMail($notifiable)
    {
        $emoji = $this->status === 'aprobada' ? '✅' : '❌';
        return (new MailMessage)
            ->subject("{$emoji} Actualización de Solicitud de Vacaciones")
            ->view('emails.vacation_rh_notification', [
                'vacationRequest' => $this->vacationRequest,
                'status'          => $this->status,
            ]);
    }
}
