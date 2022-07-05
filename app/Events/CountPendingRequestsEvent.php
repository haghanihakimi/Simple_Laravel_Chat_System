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

class CountPendingRequestsEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $receiver;
    protected $counter;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($receiver, $counter)
    {
        $this->receiver = $receiver;
        $this->counter = $counter;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //return new PrivateChannel('channel-name');
        return new PrivateChannel('ReceivedContactRequestCounter.'.$this->receiver->uid);
    }

    public function broadcastWith () {
        return [
            'user' => $this->receiver->public_uid,
            'pendings' => $this->counter       
        ];
    }
}
