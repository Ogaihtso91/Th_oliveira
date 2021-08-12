<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class FollowedSocialTecnologyUpdated extends Notification
{
    use Queueable;

    private $socialtecnology_id;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($socialtecnology_id)
    {
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
            ->subject('PLATAFORMA DE TECNOLOGIAS SOCIAIS – Uma Tecnologia Social Favorita foi Atualizada')
            ->markdown('mail._notifications._followedsocialtecnologyupdated', [
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
            'socialtecnology_id' => $this->socialtecnology_id,
        ];
    }
}
