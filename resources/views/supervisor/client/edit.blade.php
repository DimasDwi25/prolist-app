@extends('supervisor.layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">✏️ Edit Client</h1>
            <a href="{{ route('supervisor.client') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">
                ← Kembali ke Daftar Client
            </a>
        </div>

        <form action="{{ route('client.update', $client) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Grid Fields --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Name" name="name" value="{{ $client->name }}" required />
                <x-input label="Client Representative" name="client_representative" value="{{ $client->client_representative }}" />
                
                <x-input label="Phone" name="phone" value="{{ $client->phone }}" />
                <x-input label="Website" name="web" value="{{ $client->web }}" />
                
                <x-input label="City" name="city" value="{{ $client->city }}" />
                <x-input label="Province" name="province" value="{{ $client->province }}" />
                
                <x-input label="Country" name="country" value="{{ $client->country }}" />
                <x-input label="ZIP Code" name="zip_code" value="{{ $client->zip_code }}" />
            </div>

            {{-- Full Width --}}
            <x-input label="Address" name="address" value="{{ $client->address }}" />

            {{-- Notes --}}
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="4"
                    class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100 focus:outline-none shadow-sm">{{ $client->notes }}</textarea>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end gap-3 pt-4 border-t pt-6">
                <a href="{{ route('supervisor.client') }}"
                    class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded border border-gray-300 text-sm transition">
                    Batal
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded text-sm font-medium shadow transition">
                    💾 Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
