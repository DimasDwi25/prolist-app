@php
    $roleLayouts = [
        'super_admin'          => 'admin.layouts.app',
        'marketing_director'   => 'marketing-director.layouts.app',
        'supervisor marketing' => 'supervisor.layouts.app',
        'manager_marketing'    => 'supervisor.layouts.app',
        'sales_supervisor'     => 'supervisor.layouts.app',
        'marketing_admin'      => 'supervisor.layouts.app',
        'engineering_director' => 'engineering_director.layouts.app',
        'marketing_estimator' => 'supervisor.layouts.app',
    ];
    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
<div class="p-3 md:p-4 space-y-3">

    <!-- Header Title -->
    <div class="flex justify-between items-center">
        <h1 class="text-lg md:text-xl font-semibold text-gray-800 flex items-center gap-1">
            📋 Marketing Report
        </h1>
    </div>

    <!-- Success Alert -->
    @if (session('success'))
        <div class="mb-3 px-3 py-2 rounded bg-green-100 text-green-800 text-xs font-medium shadow">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table Container -->
    <div class="bg-white p-2 shadow-md rounded-md border border-gray-100 relative"
         x-data="{ loading: false }"
         x-init="
            Livewire.on('loadingStarted', () => loading = true);
            Livewire.on('loadingFinished', () => loading = false);
         ">

        <!-- Loading Overlay -->
        <div x-show="loading"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50 rounded-md">
            <div class="text-center">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">🔄 Loading Data...</h3>
                <p class="text-xs text-gray-500">Please wait while we process your request</p>
            </div>
        </div>

        <!-- Livewire Table -->
        <div wire:loading.class="opacity-50"
             wire:loading.class.remove="opacity-100"
             data-turbo="false">
            <livewire:supervisor-marketing.marketing-report-table />
        </div>
    </div>
</div>

<style>
    /* Sidebar lebih ramping */
    .sidebar {
        width: 200px !important;
    }

    /* Header Compact */
    header {
        padding: 0.4rem 1rem !important;
        min-height: 48px;
    }

    /* Tabel lebih padat */
    table td, table th {
        padding: 4px 6px !important;
        font-size: 12px !important;
        white-space: nowrap;
    }

    /* Konten Padat */
    main {
        padding: 6px !important;
    }

    /* Skeleton loading */
    @keyframes skeleton-loading {
        0% { background-position: -200px 0; }
        100% { background-position: calc(200px + 100%) 0; }
    }
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200px 100%;
        animation: skeleton-loading 1.5s infinite;
    }
</style>

<script>
    document.addEventListener('livewire:init', function () {
        Livewire.on('filterUpdated', () => {
            console.log('Filters updated');
        });
    });
</script>
@endsection
