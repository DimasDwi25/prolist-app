<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\SubClient;
use Livewire\Component;

class SubClientModal extends Component
{
    public $clientId;
    public $name;
    public $editingId = null;
    public $subClients = [];

    public $showModal = false;

    protected $listeners = ['open-sub-client-modal' => 'openModal'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'clientId' => 'required|exists:clients,id',
    ];

    public function openModal($clientId)
    {
        $this->clientId = $clientId;
        $this->showModal = true;
        $this->loadSubClients();
    }

    public function closeModal()
    {
        $this->reset(['name', 'editingId', 'showModal']);
    }

    public function loadSubClients()
    {
        $this->subClients = SubClient::where('client_id', $this->clientId)->get();
    }

    public function save()
    {
        $this->validate();

        SubClient::create([
            'client_id' => $this->clientId,
            'name' => $this->name,
        ]);

        $this->reset('name');
        $this->loadSubClients();
        session()->flash('message', 'Sub Client created.');
    }

    public function edit($id)
    {
        $sub = SubClient::findOrFail($id);
        $this->editingId = $sub->id;
        $this->name = $sub->name;
    }

    public function update()
    {
        $this->validate();

        $sub = SubClient::findOrFail($this->editingId);
        $sub->update(['name' => $this->name]);

        $this->reset(['name', 'editingId']);
        $this->loadSubClients();
        session()->flash('message', 'Updated.');
    }

    public function delete($id)
    {
        SubClient::findOrFail($id)->delete();
        $this->loadSubClients();
    }

    public function render()
    {
        return view('livewire.sub-client-modal');
    }
}
