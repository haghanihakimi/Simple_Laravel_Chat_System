<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class TubaVerification extends Notification implements ShouldQueue
{
    use Queueable;
    
    private $user;
    private $message;
    private $data;
    private $type;
    private $seen;

    public function __construct($user, $message, $type, $data, $seen)
    {
        $this->user = $user;
        $this->message = $message;
        $this->type = $type;
        $this->data = $data;
        $this->seen = $seen;
    }

    public function getUser ()
    {
        return $this->user;
    }

    public function getMessage ()
    {
        return $this->message;
    }

    public function getType ()
    {
        return $this->type;
    }

    public function getSeen ()
    {
        return $this->seen;
    }
    
    public function via($notifiable)
    {
        return ['database','mail'];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line(new HtmlString($this->data['header']))
            ->action($this->data['button'], $this->data['link']);
    }
    
    public function toArray($notifiable)
    {
        return [
            'name' => $notifiable->fname,
            'email' => $notifiable->email
        ];
    }
}
