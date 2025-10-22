<?php

namespace App\Notifications;

use App\Models\PHC;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PhcValidationRequested extends Notification implements ShouldQueue
{
   use Queueable;

    protected $phc;

    public function __construct($phc)
    {
        $this->phc = $phc;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast']; // ✅ simpan ke DB + broadcast realtime
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'PHC Validation Requested',
            'message' => "A new PHC has been created for Project {$this->phc->project->project_name}",
            'phc_id'  => $this->phc->id,
            'project' => $this->phc->project->project_number,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return [
            'data' => [
                'title' => 'PHC Validation Requested',
                'message' => "A new PHC has been created for Project {$this->phc->project->project_name}",
                'phc_id'  => $this->phc->id,
                'project' => $this->phc->project->project_number,
            ],
        ];
    }
}