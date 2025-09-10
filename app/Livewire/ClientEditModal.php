<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;

class ClientEditModal extends Component
{
    public $clientId;
    public $name, $phone, $address, $client_representative, $city, $province, $country, $zip_code, $web, $notes;

    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:50',
        'address' => 'nullable|string|max:500',
        'client_representative' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:100',
        'province' => 'nullable|string|max:100',
        'country' => 'nullable|string|max:100',
        'zip_code' => 'nullable|string|max:20',
        'web' => 'nullable|url|max:255',
        'notes' => 'nullable|string|max:1000',
    ];

    public function openClientModal($id)
    {
        $client = Client::findOrFail($id);

        $this->clientId = $client->id;
        $this->name = $client->name;
        $this->phone = $client->phone;
        $this->address = $client->address;
        $this->client_representative = $client->client_representative;
        $this->city = $client->city;
        $this->province = $client->province;
        $this->country = $client->country;
        $this->zip_code = $client->zip_code;
        $this->web = $client->web;
        $this->notes = $client->notes;

        $this->dispatch('open-client-modal-browser');
    }

    public function save()
    {
        $this->validate();

        Client::findOrFail($this->clientId)->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'client_representative' => $this->client_representative,
            'city' => $this->city,
            'province' => $this->province,
            'country' => $this->country,
            'zip_code' => $this->zip_code,
            'web' => $this->web,
            'notes' => $this->notes,
        ]);

        $this->dispatch('close-client-modal');
        $this->dispatch('refreshClientTable'); // optional: refresh Livewire Table
        session()->flash('success', 'Client updated successfully.');
    }

    public function render()
    {
        return view('livewire.client-edit-modal');
    }
}
