<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VacationRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $vacationRequest;

    public function __construct($employee, $vacationRequest)
    {
        $this->employee = $employee;
        $this->vacationRequest = $vacationRequest;
    }

    public function build()
    {
        return $this->subject('Nueva Solicitud de Vacaciones')
                    ->markdown('emails.vacationRequestNotification')
                    ->with([
                        'employee' => $this->employee,
                        'vacationRequest' => $this->vacationRequest,
                    ]);
    }
}

