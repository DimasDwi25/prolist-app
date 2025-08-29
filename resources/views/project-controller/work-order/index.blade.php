@php
    $roleLayouts = [
        'project controller'     => 'project-controller.layouts.app',
        'engineer'               => 'engineer.layouts.app',
        'engineering_manager'    => 'project-manager.layouts.app',
        'engineering_director'   => 'engineering_director.layouts.app',
    ];

    $layout = $roleLayouts[Auth::user()->role->name] ?? 'default.layouts.app';
@endphp

@extends($layout)

@section('content')
<div class="p-3 md:p-4 space-y-3">

    <!-- Header Title -->
    <div class="flex justify-between items-center">
        <h1 class="text-lg md:text-xl font-semibold text-gray-800 flex items-center gap-1">
            📄 Work Orders
        </h1>
        <a href="{{ route('engineer.work-orders.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs shadow">
            ➕ Create WO
        </a>
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
        <div wire:loading.class="opacity-50" 
             wire:loading.class.remove="opacity-100"
             wire:target="selectedYear,selectedMonth,search,filters">

            <div class="overflow-x-auto w-full">
                @livewire('project-controller.work-order-table')
            </div>
        </div>
    </div>
</div>

<style>
    /* Sidebar Ramping */
    .sidebar {
        width: 200px !important;
    }

    /* Header Compact */
    header {
        padding: 0.4rem 1rem !important;
        min-height: 48px;
    }

    /* Table Lebih Padat */
    table td, table th {
        padding: 4px 6px !important;
        font-size: 12px !important;
        white-space: nowrap;
    }

    /* Konten Padat */
    main {
        padding: 6px !important;
    }
</style>
@endsection
