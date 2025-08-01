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

        // Ambil user HO & PIC yang dipilih di form PHC
        $userIds = array_filter([
            $phc->ho_engineering_id,
            $phc->ho_marketings_id,
            $phc->pic_engineering_id,
            $phc->marketing_pic_id,
        ]);

        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $user->notify(new PhcValidationRequested($phc));
        }
    }
}
