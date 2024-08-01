<?php

namespace App\Events;

use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $senderId;
    public $receiverId;

    public function __construct($message, $senderId, $receiverId)
    {
        $this->message = $message;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        Log::info('MessageSent event created', ['message' => $message, 'senderId' => $senderId, 'receiverId' => $receiverId]);
    }

    public function broadcastOn(): Channel
    {
        $participants = [$this->senderId, $this->receiverId];
        sort($participants);

        $channelName = 'chat.' . $participants[0] . '.' . $participants[1];

        return new PresenceChannel($channelName);
    }

    public function broadcastWith()
    {
        return ['message' => $this->message, 'sender' => $this->senderId, 'receiver' => $this->receiverId];
    }
}
