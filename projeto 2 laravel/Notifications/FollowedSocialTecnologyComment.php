<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notificação enviada aos usuários quando alguém comenta uma TS que ele segue.
 */
class FollowedSocialTecnologyComment extends Notification
{
    use Queueable;

    private $comment_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($comment_id)
    {
        $this->comment_id = $comment_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('PLATAFORMA DE TECNOLOGIAS SOCIAIS – Um usuário comentou em uma Tecnologia Social favorita')
            ->markdown('mail._notifications._followedsocialtecnologycomment', [
                'comment_id' => $this->comment_id
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'comment_id' => $this->comment_id
        ];
    }
}
