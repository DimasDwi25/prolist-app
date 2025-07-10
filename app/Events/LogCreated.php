<?php

namespace App\Events;

use App\Models\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $log;

    public function __construct(Log $log)
    {
        $this->log = $log->load('user', 'category', 'responseUser');
    }

    public function broadcastOn(): Channel
    {
        return new Channel('logs.project.' . $this->log->project_id); // ← Ganti PrivateChannel ke Channel (public)
    }

    public function broadcastAs()
    {
        return 'log.created'; // ← Tambahkan nama event kustom
    }


    public function broadcastWith(): array
    {
        return ['log' => $this->log];
    }
}

