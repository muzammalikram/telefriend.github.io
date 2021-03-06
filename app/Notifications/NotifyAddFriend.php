<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\User;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NotifyAddFriend extends Notification
{
    use Queueable;
    public $friend;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($friend)
    {
        $this->friend = $friend;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database' , 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toDatabase($notifiable)
    {
        return [
            'user' => $this->friend  
        ];
    }

    public function toBroadcast($notifiable)
    { 
        return new BroadcastMessage([

            'data' => [
                'user' => $this->friend,
                'count' => $notifiable->unreadNotifications->count(),
            ]
               // 'user' => $this->friend,
               //  'count' => $notifiable->unreadNotifications->count(),
            
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
