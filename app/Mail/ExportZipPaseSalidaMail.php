<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExportZipPaseSalidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $downloadLink;

    /**
     * Create a new message instance.
     *
     * @param string $downloadUrl
     */
    public function __construct($downloadLink)
    {
        $this->downloadLink = $downloadLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Exportación de Permisos en ZIP Completada')
                    ->markdown('emails.export_zip_pasesalida')
                    ->with([
                        'url' => $this->downloadLink,
                    ]);
    }
}
