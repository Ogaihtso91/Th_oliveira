<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendUserEmail extends Notification
{
    use Queueable;

    private $nome;
    private $username;
    private $password;
    private $ts_name;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($nome, $username, $password, $ts_name)
    {
        //
        $this->nome = $nome;
        $this->username = $username;
        $this->password = $password;
        $this->ts_name = $ts_name;
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
                    ->markdown('mail.importusers', [
                        'nome' => $this->nome,
                        'ts_name' => $this->ts_name,
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
