<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notifica o usuário quando alguém responde um comentário seu em um artigo
 */
class UserCommentReplyBlog extends Notification
{
    use Queueable;

    private $comment_id;
    private $user_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($comment_id, $user_id)
    {
        $this->comment_id = $comment_id;
        $this->user_id = $user_id;
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
            ->subject('PLATAFORMA DE TECNOLOGIAS SOCIAIS - Um usuário respondeu seu comentário')
            ->markdown('mail._notifications._usercommentreplyblog', [
                'comment_id' => $this->comment_id,
                'user_id' => $this->user_id,
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
            'comment_id' => $this->comment_id,
            'user_id' => $this->user_id,
        ];
    }
}
