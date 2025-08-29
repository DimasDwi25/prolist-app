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
            📋 Quotation List
        </h1>
        <a href="{{ route('quotation.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs shadow">
            + Add Quotation
        </a>
    </div>

    <!-- Export & Import -->
    <div class="flex flex-col md:flex-row justify-between gap-2">
        <a href="{{ route('quotation.export') }}"
            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-xs shadow">
            ⬇️ Export
        </a>

        <form action="{{ route('quotation.import') }}" method="POST" enctype="multipart/form-data" class="flex gap-2 items-center">
            @csrf
            <input type="file" name="file" required class="border rounded px-2 py-1 text-xs">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs">📥 Import</button>
        </form>
    </div>

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
             wire:target="selectedYear,selectedMonth,search,filters"
             data-turbo="false">

            <div class="overflow-x-auto w-full">
                <livewire:supervisor-marketing.quotation-table />
            </div>
        </div>

        <livewire:supervisor-marketing.change-quotation-status-modal />
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
