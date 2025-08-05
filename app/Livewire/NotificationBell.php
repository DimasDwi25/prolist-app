<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public $notifications;

    protected $listeners = ['refreshNotifications' => 'loadNotifications'];


    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Auth::user()
                ?->unreadNotifications()
            ->take(10)
            ->get() ?? collect();
    }

    public function markAllAsRead()
    {
        Auth::user()?->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }

    public function markAsRead($notificationId)
    {
        Auth::user()?->unreadNotifications()->find($notificationId)?->markAsRead();
        $this->loadNotifications();
    }

    public function openApproval($phcId, $notificationId = null)
    {
        if ($notificationId) {
            Auth::user()?->unreadNotifications()->find($notificationId)?->markAsRead();
        }

        $this->dispatch('openPinModal', phcId: $phcId);
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
