<?php

namespace App\Notifications;

use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notificação para recadastrar senha de acesso a plataforma
 */
class UserResetPassword extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
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
       if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->greeting(Lang::getFromJson('Olá!'))
            ->subject(Lang::getFromJson('PLATAFORMA DE TECNOLOGIAS SOCIAIS - Notificação de alteração de senha'))
            ->line(Lang::getFromJson('Você está recebendo este e-mail para cadastrar uma nova senha de acesso para sua conta na ').config('app.name'))
            ->action(Lang::getFromJson('Cadastrar nova senha'), url(config('app.url').route('password.reset', $this->token, false)))
            ->line(Lang::getFromJson('Por favor, desconsiderar caso não tenha requisitado a alteração de sua senha.'))
            ->markdown('mail._notifications._resetpassword');
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
