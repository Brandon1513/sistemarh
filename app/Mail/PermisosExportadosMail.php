<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PermisosExportadosMail extends Mailable
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
     * Build the message.
     */
    public function build()
    {
        $url = asset("storage/exports/{$this->fileName}");

        return $this->subject('Exportación de Permisos Completada')
                    ->view('emails.permisos_exportados')
                    ->with(['url' => $url]);
    }
}
