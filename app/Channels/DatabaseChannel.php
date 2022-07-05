<?php

namespace App\Channels;

use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;
use Illuminate\Notifications\Notification;

class DatabaseChannel extends IlluminateDatabaseChannel
{
    public function buildPayload($notifiable, Notification $notification)
    {
        return [
            'id' => $notification->id,
            'user_id' => $notification->getUser()->id,
            'type' => $notification->getType(),
            'message' => $notification->getMessage(),
            'data' => $this->getData($notifiable, $notification),
            'read_at' => null,
            'seen_at' => $notification->getSeen(),
        ];
    }
}