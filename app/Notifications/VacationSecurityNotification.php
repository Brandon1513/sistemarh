<?php

namespace App\Notifications;
 
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
 
class VacationSecurityNotification extends Notification
{
    protected $vacationRequest;
 
    public function __construct($vacationRequest)
    {
        $this->vacationRequest = $vacationRequest;
    }
 
    public function via($notifiable) { return ['mail']; }
 
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔐 Aviso de Seguridad — Vacaciones Aprobadas')
            ->view('emails.vacation_security', [
                'vacationRequest' => $this->vacationRequest,
            ]);
    }
}
 
