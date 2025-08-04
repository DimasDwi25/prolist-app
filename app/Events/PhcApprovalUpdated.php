<?php

namespace App\Events;

use App\Models\PhcApproval;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PhcApprovalUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $approval;

    public function __construct(PhcApproval $approval)
    {
        $this->approval = $approval->load('phc.project');
    }

    public function broadcastOn()
    {
        return new Channel('phc-approval');
    }

    public function broadcastAs()
    {
        return 'PhcApprovalUpdated';
    }
}
