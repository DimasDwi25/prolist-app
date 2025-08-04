<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;

    public function __construct(public $notification, $userId)
    {
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new Channel("user.{$this->userId}");
    }

    public function broadcastAs()
    {
        return 'notification.sent';
    }
}