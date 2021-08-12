<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Enums\NotificationsTarget;
use App\Notifications\Channels\CustomUserChannel;

/**
 * Notifica os usuários responsáveis da TS quando uma alteração/cadastro da TS for recusada pela FBB
 */
class SocialTecnologyRefused extends Notification
{
    use Queueable;

    private $socialtecnology;
    private $updated_date;

    /**
     * Create a new notification instance.
     * @return void
     */
    public function __construct($socialtecnology, $updated_date)
    {
        $this->socialtecnology = $socialtecnology;
        $this->updated_date = $updated_date;
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
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('PLATAFORMA DE TECNOLOGIAS SOCIAIS – Alteração das Informações da Tecnologia Social Recusada')
            ->markdown('mail._notifications._socialtecnologyrefused', [
                'socialtecnology_id' => $this->socialtecnology->id,
                'socialtecnology_name' => $this->socialtecnology->socialtecnology_name,
                'updated_date' => $this->updated_date,
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
            'socialtecnology_id' => $this->socialtecnology->id,
            'socialtecnology_name' => $this->socialtecnology->socialtecnology_name,
            'updated_date' => $this->updated_date,
            'target' => NotificationsTarget::INSTITUTION,
        ];
    }
}
