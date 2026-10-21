<?php
// ─────────────────────────────────────────────────────────────────────────────
// app/Notifications/VacationApprovedNotification.php
// ─────────────────────────────────────────────────────────────────────────────
namespace App\Notifications;
 
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
 
class VacationApprovedNotification extends Notification
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
            ->subject('✅ Solicitud de Vacaciones Aprobada')
            ->view('emails.vacation_approved', [
                'vacationRequest' => $this->vacationRequest,
            ]);
    }
}
