<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notificação enviada aos usuários quando algum favorito segue uma Tecnologia Social.
 */
class FavoriteFollowSocialTecnology extends Notification
{
    use Queueable;

    private $user_id;
    private $socialtecnology_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user_id, $socialtecnology_id)
    {
        $this->user_id = $user_id;
        $this->socialtecnology_id = $socialtecnology_id;
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
            ->subject('PLATAFORMA DE TECNOLOGIAS SOCIAIS – Um usuário que você segue adicionou uma Tecnologia Social aos favoritos ')
            ->markdown('mail._notifications._favoritefollowsocialtecnology', [
                'user_id' => $this->user_id,
                'socialtecnology_id' => $this->socialtecnology_id
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
            'user_id' => $this->user_id,
            'socialtecnology_id' => $this->socialtecnology_id
        ];
    }
}
