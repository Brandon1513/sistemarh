<?php

namespace App\Notifications;
 
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
 
class EstadoPermisoActualizadoNotification extends Notification
{
    use Queueable;
    public $estado;
 
    public function __construct($estado)
    {
        $this->estado = $estado;
    }
 
    public function via($notifiable) { return ['mail']; }
 
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔄 Estado de Permiso Actualizado')
            ->view('emails.estado_permiso_actualizado', [
                'estado' => $this->estado,
            ]);
    }
 
    public function toArray($notifiable)
    {
        return ['estado' => $this->estado];
    }
}
