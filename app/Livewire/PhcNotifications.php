<?php

namespace App\Livewire;

use Livewire\Component;

class PhcNotifications extends Component
{
    protected $listeners = [
        'echo:phc.validations,phc.created' => '$refresh',
        'echo:phc.validations,phc.updated' => '$refresh',
    ];

    public function render()
    {
        return view('livewire.phc-notifications');
    }
}
