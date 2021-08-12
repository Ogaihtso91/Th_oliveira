<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

class CustomUserChannel
{
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toDatabase($notifiable);

        return $notifiable->routeNotificationFor('database')->create([
                'id' => $notification->id,

                // Custom Column
                'target' => $data['target'], //<-- comes from toDatabase() Method below

                'type' => get_class($notification),
                'data' => $data,
                'read_at' => null,
        ]);
    }

}