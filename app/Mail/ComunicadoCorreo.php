<?php

namespace App\Mail;

use App\Models\Comunicado;
use App\Services\CorreoComunicados;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComunicadoCorreo extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Comunicado $comunicado)
    {
    }

    public function envelope(): Envelope
    {
        $remitente = CorreoComunicados::remitentePara($this->comunicado);
        $iconos = ['aviso' => '📢 ', 'noticia' => '📰 ', 'red_social' => '📱 '];

        return new Envelope(
            from: new Address($remitente['correo'], $remitente['nombre']),
            subject: ($iconos[$this->comunicado->tipo] ?? '') . $this->comunicado->titulo,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.comunicado');
    }
}