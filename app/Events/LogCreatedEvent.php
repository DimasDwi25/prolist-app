<?php

namespace App\Events;

use App\Models\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log as LogFacade;

class LogCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $log;
    public $userIds;

    public function __construct(Log $log, array $userIds)
    {
        $this->log = $log;
        $this->userIds = $userIds;
    }

    public function broadcastOn()
   {
       return new Channel('log.created'); // ✅ public channel
   }

   public function broadcastAs(): string
   {
       return 'log.created';
   }

   public function broadcastWith(): array
   {
       $projectNumber = $this->log->project ? $this->log->project->project_number : 'Unknown';
       return [
           'log_id' => $this->log->id,
           'status' => $this->log->status,
           'user_ids' => $this->userIds,
           'message' => "A new log has been created for Project {$projectNumber} and requires your approval",
           'title' => 'Log Created',
           'project' => $projectNumber,
           'created_at' => now()->toISOString(),
       ];
   }
}
