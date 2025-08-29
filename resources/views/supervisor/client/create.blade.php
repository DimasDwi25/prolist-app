@php
    $roleLayouts = [
        'super_admin'              => 'admin.layouts.app',
        'marketing_director'       => 'marketing-director.layouts.app',
        'supervisor marketing'     => 'supervisor.layouts.app',
        'manager_marketing'        => 'supervisor.layouts.app',
        'sales_supervisor'         => 'supervisor.layouts.app',
        'marketing_admin'         => 'supervisor.layouts.app',
        'engineering_director'  => 'engineering_director.layouts.app',
        'marketing_estimator' => 'supervisor.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
    <div class="max-w-5xl mx-auto p-8 bg-white rounded-xl shadow-lg">
        <div class="mb-8 border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Register New Client
            </h1>
            <p class="text-gray-600 mt-1">Complete the form below to add a new client to the system</p>
        </div>

        <form action="{{ route('client.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Client Information -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Company Name <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500 ml-1">(Legal business name)</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g. PT Maju Jaya Abadi" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Contact Number <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500 ml-1">(Primary business phone)</span>
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g. +62 812 3456 7890" required>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="client_representative" class="block text-sm font-medium text-gray-700 mb-1">
                        Primary Contact <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500 ml-1">(Main point of contact)</span>
                    </label>
                    <input type="text" name="client_representative" id="client_representative"
                        value="{{ old('client_representative') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g. John Doe (Marketing Director)" required>
                    @error('client_representative')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="web" class="block text-sm font-medium text-gray-700 mb-1">
                        Website
                        <span class="text-xs text-gray-500 ml-1">(Company website URL)</span>
                    </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            https://
                        </span>
                        <input type="text" name="web" id="web" value="{{ old('web') }}"
                            class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="www.example.com">
                    </div>
                    @error('web')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address Section -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                        Business Address <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500 ml-1">(Complete street address)</span>
                    </label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g. Jl. Sudirman No. 123, Tower A, 15th Floor" required>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                        City <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="city" id="city" value="{{ old('city') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="province" class="block text-sm font-medium text-gray-700 mb-1">
                        State/Province <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="province" id="province" value="{{ old('province') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    @error('province')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                        Country <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="country" id="country" value="{{ old('country') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    @error('country')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-1">
                        Postal Code
                    </label>
                    <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('zip_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Information -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                        Additional Notes
                        <span class="text-xs text-gray-500 ml-1">(Special instructions or comments)</span>
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g. Preferred contact method, billing information, etc.">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end pt-6">
                <a href="{{ route('supervisor.client') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Register Client
                </button>
            </div>
        </form>
    </div>
@endsection