<?php

namespace App\Livewire;

use Livewire\Component;

class SubClientButton extends Component
{
    public $clientId;

    public function openModal()
    {
        $this->dispatch('openSubClientModal', clientId: $this->clientId);
    }

    public function render()
    {
        return view('livewire.sub-client-button');
    }
}
