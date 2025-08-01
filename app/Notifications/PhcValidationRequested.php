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

    public $phc;

    public function __construct(PHC $phc)
    {
        $this->phc = $phc;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    // public function toBroadcast($notifiable)
    // {
    //     return new BroadcastMessage([
    //         'message' => "PHC requires your validation for project: {$this->phc->project->project_number}",
    //         'phc_id' => $this->phc->id,
    //     ]);
    // }

    public function toArray($notifiable)
    {
        return [
            'message' => "PHC requires your validation for project: {$this->phc->project->project_number}",
            'phc_id' => $this->phc->id,
        ];
    }
}
