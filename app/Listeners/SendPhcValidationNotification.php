<?php

namespace App\Listeners;

use App\Events\PhcCreatedEvent;
use App\Models\User;
use App\Notifications\PhcValidationRequested;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPhcValidationNotification
{
    public function handle(PhcCreatedEvent $event)
    {
        $phc = $event->phc;
        $userIds = $event->userIds;

        // Kirim notifikasi ke user IDs yang sudah ditentukan
        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $user->notify(new PhcValidationRequested($phc));
        }
    }
}
