<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $roles;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $password, $roles)
    {
        $this->user = $user;
        $this->password = $password;
        $this->roles = $roles;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Bienvenido a la plataforma')
                    ->markdown('emails.new_user') // Cambia la vista si usas una vista diferente
                    ->with([
                        'user' => $this->user,
                        'password' => $this->password,
                        'roles' => $this->roles,
                    ]);
    }
}
