<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\ContentManager;
use App\AutoMessage;

class SendIncompleteSubscriptionAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contentManager;
    public $autoMessage;

    /**
     * Create a new message instance.
     * @param ContentManager $contentManager
     * @return void
     */
    public function __construct(ContentManager $contentManager)
    {
        $this->contentManager = $contentManager;
        $this->autoMessage = AutoMessage::where('award_id',$this->contentManager->getValueAwardId())->where('type', 2)->first();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_CONTACTUS_FROM'))
                    ->view('mail.incompleteSubscription')
                    ->subject( $this->autoMessage->subjectMessage() )
                    ->with([
                        'msg' => $this->autoMessage->message,
                    ]);
    }
}
