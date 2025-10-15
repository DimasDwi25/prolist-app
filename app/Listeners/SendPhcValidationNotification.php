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

        // Jika tidak ada approver yang dipilih, kirim notifikasi ke roles tertentu
        if (empty($userIds)) {
            $fallbackUsers = User::whereHas('role', function ($q) {
                $q->whereIn('name', [
                    'project manager',
                    'project controller',
                    'engineering_admin',
                    'sales_supervisor',
                    'supervisor marketing'
                ]);
            })->get();

            foreach ($fallbackUsers as $user) {
                $user->notify(new PhcValidationRequested($phc));
            }
        } else {
            $users = User::whereIn('id', $userIds)->get();

            foreach ($users as $user) {
                $user->notify(new PhcValidationRequested($phc));
            }
        }
    }
}
