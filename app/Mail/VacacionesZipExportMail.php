<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VacacionesZipExportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $zipFileName;

    /**
     * Create a new message instance.
     */
    public function __construct($zipFileName)
    {
        $this->zipFileName = $zipFileName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $url = asset('storage/exports/' . $this->zipFileName);

        return $this->subject('Exportación de Vacaciones - ZIP Completada')
                    ->markdown('emails.vacaciones_zip_export')
                    ->with('url', $url);
    }
}
