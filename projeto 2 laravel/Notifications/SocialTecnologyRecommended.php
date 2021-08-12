<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Enums\NotificationsTarget;
use App\Notifications\Channels\CustomUserChannel;

/**
 * Notifica os usuários responsáveis pela tecnologia social quando alguém a segue
 */
class SocialTecnologyRecommended extends Notification
{
    use Queueable;

    protected $user_name;
    protected $user_id;
    protected $socialtecnology;

    /**
     * Create a new notification instance.
     * @return void
     */
    public function __construct(string $user_name, $user_id, $socialtecnology)
    {
        $this->user_name = $user_name;
        $this->user_id = $user_id;
        $this->socialtecnology = $socialtecnology;
    }

    /**
     * Get the notification's delivery channels.
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', CustomUserChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('PLATAFORMA DE TECNOLOGIAS SOCIAIS – Um Usuário Adicionou sua Tecnologia Social aos Favoritos')
            ->markdown('mail._notifications._socialtecnologyrecommended', [
                'socialtecnology_id' => $this->socialtecnology->id,
                'socialtecnology_name' => $this->socialtecnology->socialtecnology_name,
                'user_name' => $this->user_name,
                'user_id' => $this->user_id,
            ]);
    }

    /**
     * Get the array representation of the notification.
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'socialtecnology_id' => $this->socialtecnology->id,
            'socialtecnology_name' => $this->socialtecnology->socialtecnology_name,
            'user_name' => $this->user_name,
            'user_id' => $this->user_id,
            'target' => NotificationsTarget::INSTITUTION,
        ];
    }
}
