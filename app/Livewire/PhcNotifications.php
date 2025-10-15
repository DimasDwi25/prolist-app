<?php

namespace App\Livewire;

use Livewire\Component;

class PhcNotifications extends Component
{
    protected $listeners = [
        'echo:phc.notifications.{auth()->id()},phc.created' => '$refresh',
        'echo:phc.notifications.{auth()->id()},phc.updated' => '$refresh',
    ];

    public function render()
    {
        return view('livewire.phc-notifications');
    }
}
