<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Enums\NotificationsTarget;
use App\Notifications\Channels\CustomUserChannel;

/**
 * Notifica os usuários responsáveis pela tecnologia social quando um novo comentário é realizado na tecnologia social
 */
class SocialTecnologyNewComment extends Notification
{
    use Queueable;

    private $comment;

    /**
     * Create a new notification instance.
     * @return void
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', CustomUserChannel::class];
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
            ->subject('PLATAFORMA DE TECNOLOGIAS SOCIAIS – Novo Comentário na Tecnologia Social')
            ->markdown('mail._notifications._socialtecnologynewcomment', [
                'comment_id' => $this->comment->id,
                'parent_id' => $this->comment->comment_id,
                'parent_content' => (!empty($this->comment->comment_id) ? $this->comment->parent_comment->content : ''),
                'user_id' => $this->comment->user_id,
                'user_name' => $this->comment->user->name,
                'content' => $this->comment->content,
                'socialtecnology_id' => $this->comment->socialtecnology_id,
                'socialtecnology_name' => $this->comment->socialtecnology->socialtecnology_name,
                'updated_date' => $this->comment->created_at->format('d/m/Y \à\s H:i\h\r\s'),
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
            'comment_id' => $this->comment->id,
            'parent_id' => $this->comment->comment_id,
            'parent_content' => (!empty($this->comment->comment_id) ? $this->comment->parent_comment->content : ''),
            'user_id' => $this->comment->user_id,
            'user_name' => $this->comment->user->name,
            'content' => $this->comment->content,
            'socialtecnology_id' => $this->comment->socialtecnology_id,
            'socialtecnology_name' => $this->comment->socialtecnology->socialtecnology_name,
            'updated_date' => $this->comment->created_at->format('d/m/Y \à\s H:i\h\r\s'),
            'target' => NotificationsTarget::INSTITUTION,
        ];
    }
}
