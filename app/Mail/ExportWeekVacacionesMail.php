<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExportWeekVacacionesMail extends Mailable
{
    use Queueable, SerializesModels;
    public $fileName;

    /**
     * Create a new message instance.
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Exportación Semanal de Vacaciones',
        );
    }

    
    public function build()
    {
        $url = asset("storage/exports/{$this->fileName}");

        return $this->subject('Exportación Semanal de Vacaciones Completada')
            ->markdown('emails.export_week_vacaciones')
            ->with(['url' => $url]);
    }
}
