<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExportWeekPaseSalidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $downloadUrl;

    /**
     * Create a new message instance.
     *
     * @param string $downloadUrl
     */
    public function __construct($downloadUrl)
    {
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Exportación Semanal de Permisos')
                    ->markdown('emails.export_week_pasesalida')
                    ->with(['url' => $this->downloadUrl]);
    }
}
