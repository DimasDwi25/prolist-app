<?php

namespace App\Livewire;

use App\Models\SubClient;
use App\Models\Client;
use Livewire\Component;

class SubClientForm extends Component
{
    public $clientId;
    public $subClientId;
    public $name;
    public $email;

    protected $listeners = ['openSubClientModal'];

    public function openSubClientModal($clientId, $subClientId = null)
    {
        $this->reset(); // Reset form
        $this->clientId = $clientId;
        $this->subClientId = $subClientId;

        if ($subClientId) {
            $subClient = SubClient::findOrFail($subClientId);
            $this->name = $subClient->name;
            $this->email = $subClient->email;
        }

        $this->dispatch('showSubClientModal');
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        SubClient::updateOrCreate(
            ['id' => $this->subClientId],
            [
                'client_id' => $this->clientId,
                'name' => $this->name,
                'email' => $this->email,
            ]
        );

        $this->dispatch('hideSubClientModal');
        $this->dispatch('refreshDatatable'); // opsional jika ingin refresh
    }

    public function render()
    {
        return view('livewire.sub-client-form');
    }
}
