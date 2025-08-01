<?php

namespace App\Events;

use App\Models\PHC;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PhcApprovalUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $phc;

    public function __construct(Phc $phc)
    {
        $this->phc = $phc->load('approvals', 'client');
    }

    public function broadcastOn(): Channel
    {
        return new Channel('phc.validations'); // sama channel biar 1 listener
    }

    public function broadcastAs(): string
    {
        return 'PhcApprovalUpdatedEvent';
    }

    public function broadcastWith(): array
    {
        return [
            'phc_id' => $this->phc->id,
            'client' => $this->phc->client->name ?? '-',
            'status' => $this->phc->status,
        ];
    }
}
