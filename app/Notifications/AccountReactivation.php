<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AccountReactivation extends Notification
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

    public function getMessage()
    {
        return $this->message;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getSeen ()
    {
        return $this->seen;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
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
            ->greeting(new HtmlString($this->data['greeting']))
            ->line(new HtmlString($this->data['header']))
            ->action($this->data['button'], $this->data['link'])
            ->line(new HtmlString($this->data['footer']));
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
            'email' => $notifiable->uid
        ];
    }
}
