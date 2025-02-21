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
            ->subject('Permiso Aprobado - Información del Solicitante')
            ->greeting('Hola, equipo de seguridad')
            ->line('Se ha aprobado un nuevo pase de salida con los siguientes datos:')
            ->line('Empleado: ' . ($this->data['nombre_empleado'] ?? 'No disponible'))
            ->line('Puesto: ' . ($this->data['puesto'] ?? 'No disponible'))
            ->line('Departamento: ' . ($this->data['departamento'] ?? 'No disponible'))
            ->line('Horario Oficial: ' . ($this->data['official_schedule'] ?? 'No disponible'))
            ->line('Hora de Entrada/Salida: ' . ($this->data['entry_exit_time'] ?? 'No disponible'))
            ->line('Fecha: ' . ($this->data['date'] ?? 'No disponible'))
            ->line('Motivo: ' . ($this->data['reason'] ?? 'No disponible'))
            ->line('Tipo de Entrada/Salida: ' . ($this->data['entry_exit_type'] ?? 'No disponible'))
            ->action('Ver Permiso', url('/permissions/' . ($this->data['id'] ?? '')))
            ->line('Gracias por su atención.');
    }
}
