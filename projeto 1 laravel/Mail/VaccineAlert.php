<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VaccineAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $vaccine;
    public function __construct($vaccine)
    {
        $this->vaccine = $vaccine;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->from('contato@aupet.fabiomarayo.com.br', 'Au!Pet')
        ->subject('Lembrete de Vacina')
        ->with([
            'vaccine' => $this->vaccine,
            'pet' => $this->vaccine->pet
        ])->view('mail.vaccine');
    }
}
