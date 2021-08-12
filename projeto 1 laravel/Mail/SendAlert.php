<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $alert;

    public function __construct($alert)
    {
        $this->alert = $alert;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $status = $this->alert->alert_type == 'D' ? 'desaparecido' : 'abandonado';
        return $this
        ->from('contato@aupet.fabiomarayo.com.br', 'Au!Pet')
        ->subject('Alerta de animal ' . $status)
        ->with(['alert' => $this->alert])->view('mail.alert');
    }
}
