<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notifica um usuário quando alguém o favoritar
 */
class UserEventRemoveFavorited extends Notification
{
    use Queueable;

    private $user_id;
    private $event_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user_id,$event_id)
    {
        $this->user_id = $user_id;
        $this->event_id = $event_id;
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
            ->subject('PLATAFORMA DE TECNOLOGIAS SOCIAIS - O usuário que você segue não tem mais interesse em um evento.')
            ->markdown('mail._notifications._usereventremovefavorited', [
                'user_id' => $this->user_id,
                'event_id' => $this->event_id,
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
            'event_id' => $this->event_id,
        ];
    }
}
