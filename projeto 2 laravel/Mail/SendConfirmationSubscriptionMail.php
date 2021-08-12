<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\SocialTecnology;
use App\AutoMessage;

class SendConfirmationSubscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $socialTecnology;
    public $autoMessage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SocialTecnology $socialTecnology)
    {
        $this->socialTecnology = $socialTecnology;
        $this->autoMessage = $this->socialTecnology->award->messages()->where('type',1)->get()->first();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_CONTACTUS_FROM'))
                    ->view('mail.subscriptionConfirmation')
                    ->subject( $this->autoMessage->subjectMessage() )
                    ->with([
                        'registration' => $this->socialTecnology->registration,
                        'msg' => $this->autoMessage->message,
                        'url' => route('front.socialtecnology.detail', ['seo_url' => $this->socialTecnology->seo_url])  ,
                    ]);
    }
}
