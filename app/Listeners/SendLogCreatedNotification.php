<?php

namespace App\Listeners;

use App\Events\LogCreatedEvent;
use App\Models\User;
use App\Notifications\LogCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLogCreatedNotification
{
    public function handle(LogCreatedEvent $event)
    {
        $log = $event->log;
        $userIds = $event->userIds;

        // Kirim notifikasi ke user IDs yang sudah ditentukan
        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $user->notify(new LogCreatedNotification($log));
        }
    }
}
