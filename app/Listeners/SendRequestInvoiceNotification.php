<?php

namespace App\Listeners;

use App\Events\RequestInvoiceCreated;
use App\Models\User;
use App\Notifications\RequestInvoiceCreated as RequestInvoiceCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendRequestInvoiceNotification implements ShouldQueue
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
    public function handle(RequestInvoiceCreated $event): void
    {
        // Get users with roles: acc_fin_manager, acc_fin_supervisor, finance_administration
        $roles = ['acc_fin_manager', 'acc_fin_supervisor', 'finance_administration'];

        $users = User::whereHas('role', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        })->get();

        // Send notification to each user
        foreach ($users as $user) {
            $user->notify(new RequestInvoiceCreatedNotification($event->requestInvoice));
        }
    }
}
