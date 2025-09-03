<?php

namespace App\Http\Controllers\supervisor_marketing;

use App\Http\Controllers\Controller;
use App\Imports\ClientImport;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB; // Tambahkan ini

class SupervisorClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return view('supervisor.client.index', compact('clients'));
    }

    public function create()
    {
        return view('supervisor.client.create');
    }

    public function show(Client $client)
    {
        return view('supervisor.client.show', compact('client'));
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
            'web' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Client::create($validated);
        return redirect()->route('supervisor.client')->with('success', 'Client created successfully.');
    }

    public function edit(Client $client)
    {
        return view('supervisor.client.edit', compact('client'));
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
        return redirect()->route('supervisor.client')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('supervisor.client')->with('success', 'Client deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new ClientImport, $request->file('file'));

        return back()->with('success', 'Client data imported successfully.');
    }
}