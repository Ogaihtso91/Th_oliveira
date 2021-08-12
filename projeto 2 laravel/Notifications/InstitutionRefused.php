<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Enums\NotificationsTarget;
use App\Notifications\Channels\CustomUserChannel;

/**
 * Notifica os usuários da instituição quando uma alteração/cadastro de Instituição for recusada pela FBB
 */
class InstitutionRefused extends Notification
{
    use Queueable;

    private $institution;
    private $updated_date;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($institution, $updated_date)
    {
        //
        $this->institution = $institution;
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
            ->subject('PLATAFORMA DE TECNOLOGIAS SOCIAIS – Alteração das Informações da Instituição Recusada ')
            ->markdown('mail._notifications._institutionrefused', [
                'institution_id' => $this->institution->id,
                'institution_name' => $this->institution->institution_name,
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
            'institution_id' => $this->institution->id,
            'institution_name' => $this->institution->institution_name,
            'updated_date' => $this->updated_date,
            'target' => NotificationsTarget::INSTITUTION,
        ];
    }
}
