@php
    $roleLayouts = [
        'super_admin'              => 'admin.layouts.app',
        'marketing_director'       => 'marketing-director.layouts.app',
        'supervisor marketing'     => 'supervisor.layouts.app',
        'manager_marketing'        => 'supervisor.layouts.app',
        'sales_supervisor'         => 'supervisor.layouts.app',
        'marketing_admin'         => 'supervisor.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Client Profile</h1>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">{{ $client->name }}</span>
                    <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Active</span>
                </div>
            </div>
        </div>

        <div class="flex space-x-3 w-full sm:w-auto">
            <a href="{{ route('supervisor.client') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm">
                ← Back to List
            </a>
            <a href="{{ route('client.edit', $client) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Edit Profile
            </a>
        </div>
    </div>

    {{-- Client Information Card --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        {{-- Client Summary Bar --}}
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-white shadow-sm border border-gray-100">
                        <span class="text-xl font-bold text-blue-600">{{ strtoupper(substr($client->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">{{ $client->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $client->client_representative ?? 'No primary contact' }}</p>
                    </div>
                </div>
                <div class="mt-3 sm:mt-0">
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 text-xs font-medium bg-white rounded-full border border-gray-200 shadow-sm">
                            <span class="w-2 h-2 inline-block rounded-full bg-green-500 mr-1"></span>
                            Active Client
                        </span>
                        <button class="p-2 rounded-lg bg-white border border-gray-200 shadow-sm hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6">
            {{-- Main Information Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Basic Information Column --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Contact Card --}}
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Contact Information
                        </h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</p>
                                    <p class="mt-1 text-sm font-medium text-gray-900 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ $client->phone ?? 'Not provided' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Website</p>
                                    @if($client->web)
                                    <a href="{{ Str::startsWith($client->web, 'http') ? $client->web : 'https://'.$client->web }}" 
                                       target="_blank" 
                                       class="mt-1 text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                        {{ $client->web }}
                                    </a>
                                    @else
                                    <p class="mt-1 text-sm text-gray-500">Not provided</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Address Card --}}
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Location Information
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Address</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $client->address ?? 'Not provided' }}</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">City</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $client->city ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Province</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $client->province ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Postal Code</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $client->zip_code ?? 'Not provided' }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Country</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $client->country ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes Column --}}
                <div class="space-y-6">
                    {{-- Quick Actions --}}
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="#" class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-blue-600 p-2 rounded-lg hover:bg-blue-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                </svg>
                                <span>Create Quotation</span>
                            </a>
                            <a href="#" class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-blue-600 p-2 rounded-lg hover:bg-blue-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span>Add Contact</span>
                            </a>
                            <a href="#" class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-blue-600 p-2 rounded-lg hover:bg-blue-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span>Set Reminder</span>
                            </a>
                        </div>
                    </div>

                    {{-- Notes Card --}}
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 h-full">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Client Notes
                            </h3>
                            <button class="text-xs font-medium text-blue-600 hover:text-blue-800">Add Note</button>
                        </div>
                        @if($client->notes)
                            <div class="prose prose-sm max-w-none text-gray-700">
                                {!! nl2br(e($client->notes)) !!}
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No notes available for this client</p>
                                <button class="mt-2 text-sm font-medium text-blue-600 hover:text-blue-800">Add your first note</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection