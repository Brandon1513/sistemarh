<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionSeguridadPermiso extends Notification
{
    use Queueable;

    protected $data;

    public function __construct($data)
    {
        \Log::info('Notificación recibida con datos:', $data); // Log para depurar
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['mail']; // Solo correo electrónico
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔐 Permiso Aprobado - Información del Solicitante')
            ->view('emails.notificacion_seguridad_permiso', [
                'data' => $this->data,
            ]);
    }
}
