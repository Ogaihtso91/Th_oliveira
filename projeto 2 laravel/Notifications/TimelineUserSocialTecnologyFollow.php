<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Enums\NotificationsTarget;
use App\Notifications\Channels\CustomUserChannel;

/**
 * Timeline - Quando o usuário favorita uma tecnologia social.
 */
class TimelineUserSocialTecnologyFollow extends Notification
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
        return [CustomUserChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        /*return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');*/
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
            'target' => NotificationsTarget::PROFILE,
        ];
    }
}
