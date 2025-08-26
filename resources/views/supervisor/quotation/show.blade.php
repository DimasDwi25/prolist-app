@php
    $roleLayouts = [
        'super_admin'              => 'admin.layouts.app',
        'marketing_director'       => 'marketing-director.layouts.app',
        'supervisor marketing'     => 'supervisor.layouts.app',
        'manager_marketing'        => 'supervisor.layouts.app',
        'sales_supervisor'         => 'supervisor.layouts.app',
        'marketing_admin'         => 'supervisor.layouts.app',
        'engineering_director'  => 'engineering_director.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m4 0v-6a6 6 0 00-6-6h-2a6 6 0 00-6 6v6" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Quotation Details</h1>
                <p class="text-sm text-gray-500">Reference: {{ $quotation->no_quotation }}</p>
                <p class="text-xs text-gray-400 mt-1">Last updated: {{ $quotation->updated_at->format('M j, Y H:i') }}</p>
            </div>
        </div>

        <div class="flex space-x-3">
            <a href="{{ route('quotation.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                ← Back to List
            </a>
            
            @if($quotation->status !== 'A' && $quotation->status !== 'E')
            <a href="{{ route('quotation.edit', $quotation->quotation_number) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                Edit Quotation
            </a>
            @endif
        </div>
    </div>

    {{-- Status Alert --}}
    @if($quotation->status === 'E')
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700 font-medium">
                    This quotation has been cancelled and is no longer active.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Quotation Card --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        {{-- Tab Navigation --}}
        <div x-data="{ activeTab: 'details' }" class="border-b border-gray-200">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6">
                    <button @click="activeTab = 'details'" 
                        :class="activeTab === 'details' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Details
                    </button>
                    <button @click="activeTab = 'client'" 
                        :class="activeTab === 'client' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Client
                    </button>
                    <button @click="activeTab = 'history'" 
                        :class="activeTab === 'history' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        History
                    </button>
                    @if($quotation->notes)
                    <button @click="activeTab = 'notes'" 
                        :class="activeTab === 'notes' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Notes
                    </button>
                    @endif
                </nav>
            </div>

            {{-- Tab Content --}}
            <div class="p-6">
                {{-- Quotation Details Tab --}}
                <div x-show="activeTab === 'details'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Left Column --}}
                        <div class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Quotation Number</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $quotation->no_quotation }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Title</p>
                                <p class="mt-1 text-lg font-medium text-gray-900">{{ $quotation->title_quotation }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Quotation Value</p>
                                <p class="mt-1 text-2xl font-bold text-blue-600">IDR {{ number_format($quotation->quotation_value, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        {{-- Right Column --}}
                        <div class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</p>
                                <p class="mt-2">
                                    @php
                                        $statusClasses = [
                                            'A' => 'bg-green-100 text-green-800',
                                            'D' => 'bg-yellow-100 text-yellow-800',
                                            'E' => 'bg-red-100 text-red-800',
                                            'F' => 'bg-purple-100 text-purple-800',
                                            'O' => 'bg-blue-100 text-blue-800'
                                        ];
                                        $statusIcons = [
                                            'A' => '✓',
                                            'D' => '⏳',
                                            'E' => '❌',
                                            'F' => '⚠️',
                                            'O' => '🕒'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full {{ $statusClasses[$quotation->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        <span class="mr-1">{{ $statusIcons[$quotation->status] ?? '' }}</span>
                                        @switch($quotation->status)
                                            @case('A') Completed @break
                                            @case('D') No PO Yet @break
                                            @case('E') Cancelled @break
                                            @case('F') Project Lost @break
                                            @case('O') On Going @break
                                            @default Unknown Status
                                        @endswitch
                                    </span>
                                </p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</p>
                                <div class="mt-2 space-y-1">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Quotation Date:</span>
                                        <span class="font-medium">{{ $quotation->quotation_date->format('F j, Y') }}</span>
                                    </div>
                                    @if($quotation->inquiry_date)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Inquiry Date:</span>
                                        <span class="font-medium">{{ $quotation->inquiry_date->format('F j, Y') }}</span>
                                    </div>
                                    @endif
                                    @if($quotation->revision_quotation_date)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Revision Date:</span>
                                        <span class="font-medium">{{ $quotation->revision_quotation_date->format('F j, Y') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</p>
                                <p class="mt-1 text-gray-900">{{ $quotation->quotation_weeks }} weeks</p>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Information Section --}}
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Additional Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Revision</p>
                                <p class="mt-1 text-gray-900">{{ $quotation->revisi ? 'Revision '.$quotation->revisi : 'Original' }}</p>
                            </div>
                            
                            @if($quotation->client_pic)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Client PIC</p>
                                <p class="mt-1 text-gray-900">{{ $quotation->client_pic }}</p>
                            </div>
                            @endif
                            
                            @if($quotation->created_by)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</p>
                                <p class="mt-1 text-gray-900">{{ $quotation->user->name }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Client Information Tab --}}
                <div x-show="activeTab === 'client'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Client Basic Info --}}
                        <div class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Client Name</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $quotation->client->name }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Point of Contact</p>
                                <p class="mt-1 text-gray-900">{{ $quotation->client_pic ?? 'Not specified' }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Client Representative</p>
                                <p class="mt-1 text-gray-900">{{ $quotation->client->client_representative ?? 'Not specified' }}</p>
                            </div>
                        </div>

                        {{-- Client Contact Info --}}
                        <div class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Information</p>
                                <div class="mt-2 space-y-2">
                                    @if($quotation->client->phone)
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <span>{{ $quotation->client->phone }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($quotation->client->email)
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ $quotation->client->email }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Client Address Section --}}
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Client Address</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Address</p>
                                <p class="mt-1 text-gray-900">{{ $quotation->client->address ?? 'Not specified' }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Location</p>
                                <p class="mt-1 text-gray-900">
                                    {{ $quotation->client->city ?? 'Not specified' }}{{ $quotation->client->province ? ', '.$quotation->client->province : '' }}
                                </p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Country</p>
                                <p class="mt-1 text-gray-900">{{ $quotation->client->country ?? 'Not specified' }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Postal Code</p>
                                <p class="mt-1 text-gray-900">{{ $quotation->client->zip_code ?? 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Revision History Tab --}}
                <div x-show="activeTab === 'history'" class="space-y-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            {{-- Creation Event --}}
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Created by <span class="font-medium text-gray-900">{{ $quotation->user->name }}</span></p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $quotation->created_at->format('Y-m-d') }}">{{ $quotation->created_at->format('M j, Y') }}</time>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2 ml-11 rounded-md bg-blue-50 p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-blue-800">Initial Quotation Created</h3>
                                                <div class="mt-2 text-sm text-blue-700">
                                                    <p>Original version with number {{ $quotation->no_quotation }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            {{-- Revision Event --}}
                            @if($quotation->revision_quotation_date)
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Revised by <span class="font-medium text-gray-900">{{ $quotation->user->name }}</span></p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $quotation->revision_quotation_date->format('Y-m-d') }}">{{ $quotation->revision_quotation_date->format('M j, Y') }}</time>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2 ml-11 rounded-md bg-yellow-50 p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-yellow-800">Quotation Revised</h3>
                                                <div class="mt-2 text-sm text-yellow-700">
                                                    <p>Revision {{ $quotation->revisi ?? '1' }} with updates</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif

                            @php
                                $statusConfig = [
                                    'A' => [
                                        'bg' => 'bg-green-500',
                                        'title' => 'Quotation Completed',
                                        'description' => 'Project completed with PO',
                                        'icon' => 'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z',
                                        'textColor' => 'text-green-800',
                                        'boxColor' => 'bg-green-50',
                                    ],
                                    'D' => [
                                        'bg' => 'bg-yellow-500',
                                        'title' => 'No PO Yet',
                                        'description' => 'Quotation approved but PO not received yet',
                                        'icon' => 'M10 18a8 8 0 100-16 8 8 0 000 16zm.75-12.75a.75.75 0 00-1.5 0v5.25a.75.75 0 00.75.75h3.5a.75.75 0 000-1.5H10.75V5.25z',
                                        'textColor' => 'text-yellow-800',
                                        'boxColor' => 'bg-yellow-50',
                                    ],
                                    'E' => [
                                        'bg' => 'bg-gray-500',
                                        'title' => 'Cancelled',
                                        'description' => 'Quotation cancelled by client or internal decision',
                                        'icon' => 'M6 18L18 6M6 6l12 12',
                                        'textColor' => 'text-gray-800',
                                        'boxColor' => 'bg-gray-50',
                                    ],
                                    'F' => [
                                        'bg' => 'bg-red-500',
                                        'title' => 'Lost Bid',
                                        'description' => 'Quotation rejected, bid lost',
                                        'icon' => 'M12 2a10 10 0 100 20 10 10 0 000-20zm-1 5h2v6h-2V7zm0 8h2v2h-2v-2z',
                                        'textColor' => 'text-red-800',
                                        'boxColor' => 'bg-red-50',
                                    ],
                                    'O' => [
                                        'bg' => 'bg-blue-500',
                                        'title' => 'On Going',
                                        'description' => 'Quotation is still in process',
                                        'icon' => 'M12 4v1m0 10v1m8-8h-1M5 12H4m15.364-7.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707',
                                        'textColor' => 'text-blue-800',
                                        'boxColor' => 'bg-blue-50',
                                    ],
                                ];

                                $status = $quotation->status;
                            @endphp

                            @if(isset($statusConfig[$status]))
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full {{ $statusConfig[$status]['bg'] }} flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="{{ $statusConfig[$status]['icon'] }}" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $statusConfig[$status]['title'] }} by 
                                                        <span class="font-medium text-gray-900">{{ $quotation->user->name }}</span>
                                                    </p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time datetime="{{ $quotation->updated_at->format('Y-m-d') }}">
                                                        {{ $quotation->updated_at->format('M j, Y') }}
                                                    </time>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2 ml-11 rounded-md {{ $statusConfig[$status]['boxColor'] }} p-4">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 {{ $statusConfig[$status]['textColor'] }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="{{ $statusConfig[$status]['icon'] }}" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium {{ $statusConfig[$status]['textColor'] }}">
                                                        {{ $statusConfig[$status]['title'] }}
                                                    </h3>
                                                    <div class="mt-2 text-sm {{ $statusConfig[$status]['textColor'] }}">
                                                        <p>{{ $statusConfig[$status]['description'] }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- Notes Tab --}}
                @if($quotation->notes)
                <div x-show="activeTab === 'notes'" class="space-y-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Additional Notes</p>
                        <div class="prose max-w-none text-gray-700">
                            {!! nl2br(e($quotation->notes)) !!}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection