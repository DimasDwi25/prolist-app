<?php

namespace App\Notifications;

use App\Models\PHC;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PhcCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $phc;

    public function __construct(PHC $phc)
    {
        $this->phc = $phc;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "PHC berhasil dibuat untuk Project {$this->phc->project->project_name}",
            'phc_id'  => $this->phc->id,
            'project' => $this->phc->project->project_number,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return [
            'data' => [
                'message' => "PHC berhasil dibuat untuk Project {$this->phc->project->project_name}",
                'phc_id'  => $this->phc->id,
                'project' => $this->phc->project->project_number,
            ],
        ];
    }
}
