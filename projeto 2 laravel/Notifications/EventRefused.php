<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Enums\NotificationsTarget;
use App\Notifications\Channels\CustomUserChannel;

/**
 * Notifica os usuários da instituição quando uma alteração/cadastro de Evento for recusada pela FBB
 */
class EventRefused extends Notification
{
    use Queueable;

    private $event;
    private $updated_date;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($event, $updated_date)
    {
        //
        $this->event = $event;
        $this->updated_date = $updated_date;
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
            ->subject('Fundação Banco do Brasil - Nova Plataforma de Tecnologia Social')
            ->markdown('mail._notifications._eventrefused', [
                'event_id' => $this->event->id,
                'name' => $this->event->title,
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
            'event_id' => $this->event->id,
            'name' => $this->event->title,
            'updated_date' => $this->updated_date,
            'target' => NotificationsTarget::INSTITUTION,
        ];
    }
}