<?php

namespace App\Notifications;

use App\Models\PHC;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class PhcMissingEngineeringNotification extends Notification
{
    use Queueable;

    protected $phc;

    public function __construct(PHC $phc)
    {
        $this->phc = $phc;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'title' => 'PHC Created Without Engineering PIC',
            'body' => "PHC untuk project {$this->phc->project->name} belum memiliki HO atau PIC Engineering.",
            'phc_id' => $this->phc->id,
        ]);
    }
}

