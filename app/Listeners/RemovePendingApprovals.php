<?php

namespace App\Listeners;

use App\Events\PhcApprovalUpdatedEvent;
use App\Models\User;
use App\Notifications\PhcValidationRequested;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class RemovePendingApprovals
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PhcApprovalUpdatedEvent $event)
    {
        $phc = $event->phc;
        
        if ($phc->ho_engineering_id) {
            // Hapus notifikasi untuk PM/PC/SuperAdmin
            Notification::where('type', PhcValidationRequested::class)
                ->where('notifiable_type', User::class)
                ->whereIn('notifiable_id', function($query) {
                    $query->select('id')
                        ->from('users')
                        ->whereHas('role', function($q) {
                            $q->whereIn('name', ['project manager', 'project controller', 'super_admin']);
                        });
                })
                ->delete();
        }
    }
}
