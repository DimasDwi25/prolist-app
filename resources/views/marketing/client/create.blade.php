@extends(Auth::user()->role->name === 'super_admin' ? 'admin.layouts.app' : 'marketing.layouts.app')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-6">🧾 Create New Client</h1>

    <form action="{{ route('client.store') }}" method="POST" class="bg-white p-8 rounded-lg shadow-md w-full max-w-4xl">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-gray-700 font-semibold mb-1">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring focus:ring-blue-200"
                    placeholder="e.g. PT Maju Jaya">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-gray-700 font-semibold mb-1">Phone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring focus:ring-blue-200"
                    placeholder="e.g. +62 812 3456 7890">
                @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="client_representative" class="block text-gray-700 font-semibold mb-1">Client
                    Representative</label>
                <input type="text" name="client_representative" id="client_representative"
                    value="{{ old('client_representative') }}"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring focus:ring-blue-200"
                    placeholder="e.g. Budi Santoso">
                @error('client_representative')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="web" class="block text-gray-700 font-semibold mb-1">Website</label>
                <input type="text" name="web" id="web" value="{{ old('web') }}"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring focus:ring-blue-200"
                    placeholder="e.g. www.example.com">
                @error('web')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="address" class="block text-gray-700 font-semibold mb-1">Address</label>
                <input type="text" name="address" id="address" value="{{ old('address') }}"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring focus:ring-blue-200"
                    placeholder="Full office address">
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="city" class="block text-gray-700 font-semibold mb-1">City</label>
                <input type="text" name="city" id="city" value="{{ old('city') }}"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring focus:ring-blue-200">
                @error('city')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="province" class="block text-gray-700 font-semibold mb-1">Province</label>
                <input type="text" name="province" id="province" value="{{ old('province') }}"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring focus:ring-blue-200">
                @error('province')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="country" class="block text-gray-700 font-semibold mb-1">Country</label>
                <input type="text" name="country" id="country" value="{{ old('country') }}"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring focus:ring-blue-200">
                @error('country')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="zip_code" class="block text-gray-700 font-semibold mb-1">ZIP Code</label>
                <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code') }}"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring focus:ring-blue-200">
                @error('zip_code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="notes" class="block text-gray-700 font-semibold mb-1">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring focus:ring-blue-200"
                    placeholder="Any special notes...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end mt-8">
            <a href="{{ route('marketing.client') }}" class="mr-4 text-gray-600 hover:underline">Cancel</a>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow transition duration-200">
                Create Client
            </button>
        </div>
    </form>
@endsection