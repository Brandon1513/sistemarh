<?php

namespace App\Notifications;

use App\Models\Comunicado;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Avisos del flujo de revisión: al revisor (pendiente) y al autor (aprobado o devuelto).
 */
class ComunicadoRevision extends Notification
{
    public function __construct(
        private Comunicado $comunicado,
        private string $motivo, // pendiente | aprobado | devuelto
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $c = $this->comunicado;
        $nombre = explode(' ', trim($notifiable->name))[0];

        return match ($this->motivo) {
            'pendiente' => (new MailMessage)
                ->subject("Comunicado por revisar: {$c->titulo}")
                ->greeting("Hola, {$nombre}")
                ->line(($c->autor?->name ?? 'Alguien') . " te pidió revisar un comunicado antes de publicarlo:")
                ->line("**{$c->titulo}**")
                ->action('Revisar comunicado', route('comunicados.admin.revisar', $c))
                ->line('Puedes aprobarlo y publicarlo, o devolverlo con tus comentarios.'),

            'aprobado' => (new MailMessage)
                ->subject("Tu comunicado fue aprobado: {$c->titulo}")
                ->greeting("Hola, {$nombre}")
                ->line(($c->revisor?->name ?? 'RH') . " aprobó tu comunicado y ya está publicado:")
                ->line("**{$c->titulo}**")
                ->action('Ver mis comunicados', route('comunicados.admin.index')),

            'devuelto' => (new MailMessage)
                ->subject("Tu comunicado necesita cambios: {$c->titulo}")
                ->greeting("Hola, {$nombre}")
                ->line(($c->revisor?->name ?? 'RH') . " revisó tu comunicado y pidió algunos cambios:")
                ->line("**{$c->titulo}**")
                ->line('> ' . $c->comentario_revision)
                ->action('Corregir comunicado', route('comunicados.admin.edit', $c)),
        };
    }
}