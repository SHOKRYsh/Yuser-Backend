<?php

namespace Modules\Chat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewMessage implements ShouldBroadcast
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

        Log::info("🚀 Event dispatched from sender {$senderId} to reciever: {$receiverId}");

    }

    public function broadcastOn()
    {
        Log::info("📡 NewMessage broadcastOn called from sender {$this->senderId} to reciever: {$this->receiverId}");
        return new Channel('chat.'.$this->receiverId);
    }

    public function broadcastAs()
    {
        Log::info("📣 Event broadcast as: new-message");
        return 'new-message';
    }
}
