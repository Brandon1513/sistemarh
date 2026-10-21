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
 
    public function via($notifiable) { return ['mail']; }
 
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🚪 Nuevo Pase de Salida Solicitado')
            ->view('emails.permission_requested', [
                'permission'  => $this->permission,
                'notifiable'  => $notifiable,
            ]);
    }
}
