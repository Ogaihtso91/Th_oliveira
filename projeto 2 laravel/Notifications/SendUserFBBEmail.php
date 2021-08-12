<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendUserFBBEmail extends Notification
{
    use Queueable;

    private $name;
    private $email;
    private $password;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($name, $email, $password)
    {
        //
        $this->nome = $name;
        $this->username = $email;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
                    ->markdown('mail.importusersfbb', [
                        'nome' => $this->nome,
                        'username' => $this->username,
                        'password' => $this->password,
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
