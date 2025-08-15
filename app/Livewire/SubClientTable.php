<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SubClient;

class SubClientTable extends Component
{
    public $clientId;
    public $subClients = [];

    public function mount($clientId)
    {
        $this->clientId = $clientId;
        $this->loadSubClients();
    }

    public function loadSubClients()
    {
        $this->subClients = SubClient::where('client_id', $this->clientId)->latest()->get();
    }

    public function render()
    {
        return view('livewire.sub-client-table');
    }
}
