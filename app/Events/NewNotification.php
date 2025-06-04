<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId;

    public function __construct($message, $userId)
    {
        $this->message = $message;
        $this->userId = $userId;
        Log::info("🚀 Event dispatched to user {$userId} with message: {$message}");

    }

    public function broadcastOn()
    {

        Log::info("📡 NewNotification broadcastOn called for user {$this->userId}");
        return new Channel('notifications.'.$this->userId);
    }

    public function broadcastAs()
    {
        Log::info("📣 Event broadcast as: new-notification");
        return 'new-notification';
    }
}
