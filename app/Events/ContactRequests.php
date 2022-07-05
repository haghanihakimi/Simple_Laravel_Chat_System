<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Helpers\RedisController;
use App\Http\Resources\ContactsRelationshipResource;
use Illuminate\Support\Facades\Redis;

class ContactRequests implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $receiver;
    protected $sender;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($receiver, $sender)
    {
        $this->receiver = $receiver;
        $this->sender = $sender;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //return new PrivateChannel('channel-name');
        return new PrivateChannel('newContactRequest.'.$this->receiver->uid);
    }

    public function broadcastWith () {
        $notification_sound = json_decode(Redis::get('profiles:username:'.$this->receiver->uid));
        return [
            'interacts' => (new ContactsRelationshipResource($this->receiver))->target($this->sender),
            'contents' => '<a href="#" style="padding:12px;text-decoration:none;color:#fff;" target="_self">You have new contact request from '.$this->sender->fname.' '.$this->sender->sname.'</a>',        
            'notification_sound' => $notification_sound->notification_sound
        ];
    }
}
