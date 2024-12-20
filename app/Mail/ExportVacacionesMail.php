<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExportVacacionesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function build()
    {
        $url = asset("storage/exports/{$this->fileName}");

        return $this->subject('Exportación de Vacaciones Completada')
                    ->markdown('emails.export_libro_mayor_vacaciones')
                    ->with(['url' => $url]);
    }
}
