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
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📋 Quotation List Sales Report</h1>
    </div>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-md bg-green-100 text-green-800 font-medium shadow">
            {{ session('success') }}
        </div>
    @endif

   
    <!-- Main Table Container with Loading State -->
    <div class="bg-white p-4 shadow-md rounded-xl border border-gray-100" x-data="{ loading: false }" x-init="
                // Listen for Livewire events
                Livewire.on('loadingStarted', () => { loading = true });
                Livewire.on('loadingFinished', () => { loading = false });
             ">

        <!-- Custom Loading Overlay -->
        <div x-show="loading" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50 rounded-xl">
            <div class="text-center">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">🔄 Loading Data...</h3>
                <p class="text-sm text-gray-500">Please wait while we process your request</p>
            </div>
        </div>

        <!-- Table Component -->
        <div wire:loading.class="opacity-50" wire:loading.class.remove="opacity-100" data-turbo="false">
            <livewire:supervisor-marketing.sales-report-table />
        </div>
    </div>

    <!-- Additional Styling -->
    <style>
        /* Loading Animation Enhancements */
        .livewire-loading {
            position: relative;
        }

        .livewire-loading::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(2px);
            z-index: 10;
        }

        /* Smooth transitions for filter changes */
        .table-container {
            transition: all 0.3s ease-in-out;
        }

        /* Enhanced skeleton animation */
        @keyframes skeleton-loading {
            0% {
                background-position: -200px 0;
            }

            100% {
                background-position: calc(200px + 100%) 0;
            }
        }

        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200px 100%;
            animation: skeleton-loading 1.5s infinite;
        }
    </style>

    <!-- JavaScript for enhanced loading experience -->
    <!-- Add this to your view if you want dynamic filter updates -->
    <script>
        document.addEventListener('livewire:init', function () {
            Livewire.on('filterUpdated', () => {
                // You can add custom behavior when filters change
                console.log('Filters were updated');
            });
        });
    </script>


@endsection