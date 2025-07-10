@extends('admin.layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Client</h1>

<form action="{{ route('client.update', $client) }}" method="POST" class="bg-white p-6 rounded shadow-md w-full max-w-2xl">
    @csrf
    @method('PUT')

    <x-input label="Name" name="name" value="{{ $client->name }}" />
    <x-input label="Address" name="address" value="{{ $client->address }}" />
    <x-input label="Phone" name="phone" value="{{ $client->phone }}" />
    <x-input label="Client Representative" name="client_representative" value="{{ $client->client_representative }}" />
    <x-input label="City" name="city" value="{{ $client->city }}" />
    <x-input label="Province" name="province" value="{{ $client->province }}" />
    <x-input label="Country" name="country" value="{{ $client->country }}" />
    <x-input label="ZIP Code" name="zip_code" value="{{ $client->zip_code }}" />
    <x-input label="Website" name="web" value="{{ $client->web }}" />
    <div class="mb-4">
        <label class="block text-gray-700">Notes</label>
        <textarea name="notes" rows="3" class="w-full border px-4 py-2 rounded">{{ $client->notes }}</textarea>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('admin.client') }}" class="mr-4 text-gray-600 hover:underline">Cancel</a>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
    </div>
</form>
@endsection
