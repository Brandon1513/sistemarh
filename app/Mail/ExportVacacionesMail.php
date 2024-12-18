<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExportVacacionesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $filePath;
    public $subjectLine;

    public function __construct($filePath, $subjectLine)
    {
        $this->filePath = $filePath;
        $this->subjectLine = $subjectLine;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->markdown('emails.export_libro_mayor_vacaciones')
                    ->attachFromStorage($this->filePath);
    }
}
