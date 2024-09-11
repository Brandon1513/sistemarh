<?php
namespace App\Notifications;

use App\Models\Permission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PermissionRequested extends Notification
{
    use Queueable;

    protected $permission;

    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    public function via($notifiable)
    {
        return ['mail'];  // O cualquier otro canal de notificación
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
        ->subject('Nuevo Permiso Solicitado')
        ->greeting('Hola ' . $notifiable->name)
        ->line('El empleado ' . $this->permission->user->name . ' ha solicitado un permiso.') // Cambié 'usuario' a 'user'
        ->action('Ver Permiso', url('/permissions/' . $this->permission->id))
        ->line('Gracias por usar nuestra aplicación!');
    }
}

