<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PermisosZipExportadosMail extends Mailable
{
    use SerializesModels;

    public $downloadLink; // Propiedad pública para el enlace

    /**
     * Crear una nueva instancia del mensaje.
     *
     * @param string $downloadLink
     */
    public function __construct($downloadLink)
    {
        $this->downloadLink = $downloadLink; // Establecer el enlace en el constructor
    }

    /**
     * Construir el mensaje.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.permisos_zip_exportados')
                    ->subject('Tu archivo ZIP está listo para descargar')
                    ->with([
                        'downloadLink' => $this->downloadLink, // Pasar el enlace a la vista
                    ]);
    }
}
