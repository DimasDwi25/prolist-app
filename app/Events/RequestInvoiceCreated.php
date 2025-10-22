<?php

namespace App\Events;

use App\Models\RequestInvoice;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RequestInvoiceCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $requestInvoice;
    public $userIds;

    public function __construct(RequestInvoice $requestInvoice, array $userIds)
    {
        $this->requestInvoice = $requestInvoice;
        $this->userIds = $userIds;
    }

    public function broadcastOn()
    {
        return new Channel('request.invoice.created'); // ✅ public channel
    }

    public function broadcastAs(): string
    {
        return 'request.invoice.created';
    }

    public function broadcastWith(): array
    {
        $projectNumber = $this->requestInvoice->project ? $this->requestInvoice->project->project_number : 'Unknown';
        return [
            'request_invoice_id' => $this->requestInvoice->id,
            'status' => $this->requestInvoice->status,
            'user_ids' => $this->userIds,
            'message' => "Request Invoice for project {$projectNumber} has been created.",
            'created_at' => now()->toISOString(),
        ];
    }

}
