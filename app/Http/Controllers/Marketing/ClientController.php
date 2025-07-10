<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    //
    public function index()
    {
        $clients = Client::all();
        return view('marketing.client.index', compact('clients'));
    }

    public function create()
    {
        return view('marketing.client.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'client_representative' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'web' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Client::create($validated);
        return redirect()->route('marketing.client')->with('success', 'Client created successfully.');
    }

    public function edit(Client $client)
    {
        return view('marketing.client.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'client_representative' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'web' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);
        return redirect()->route('marketing.client')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('marketing.client')->with('success', 'Client deleted successfully.');
    }
}
