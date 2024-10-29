<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserNotification extends Notification
{
    use Queueable;

    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Important Notification')
            ->line($this->message)
            ->action('Visit Website', url('/'))
            ->line('Thank you for using our application!')
            ->cc('dowarah.dayananda35@gmail.com'); // Add your fixed CC email here
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }
}
