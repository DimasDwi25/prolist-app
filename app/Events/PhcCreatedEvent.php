<?php

namespace App\Events;

use App\Models\PHC;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PhcCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $phc;
    public $userIds;

    public function __construct(PHC $phc, array $userIds)
    {
        $this->phc = $phc;
        $this->userIds = $userIds;
    }

    public function broadcastOn(): array
    {
        // Public channel supaya semua bisa dengar (tes dulu)
        return [new Channel('phc.notifications')];
    }

    public function broadcastAs(): string
    {
        return 'phc.created';
    }

    public function broadcastWith(): array
    {
        return [
            'phc_id' => $this->phc->id,
            'status' => $this->phc->status,
            'user_ids' => $this->userIds,
            'message' => "PHC #{$this->phc->id} waiting for approval."
        ];
    }
}
