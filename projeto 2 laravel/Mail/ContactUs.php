<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactUs extends Mailable
{
    use Queueable, SerializesModels;

    protected $data_message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Array $data_message)
    {
        $this->data_message = $data_message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $message_obj = $this->subject($this->data_message['subject'])
            ->view('mail.contactus')
            ->with([
                'username' => $this->data_message['name'],
                'useremail' => $this->data_message['email'],
                'userphone' => $this->data_message['phone'],
                'cf_message' => $this->data_message['message'],
            ]);

        if (!empty($this->data_message['file'])) {
            $message_obj->attach($this->data_message['file']->getRealPath(),
                [
                    'as' => $this->data_message['file']->getClientOriginalName(),
                    'mime' => $this->data_message['file']->getClientMimeType(),
                ]);
        }

        return $message_obj;
    }
}
