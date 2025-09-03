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

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
        <h1 class="text-lg md:text-xl font-semibold text-gray-800">📋 Client List</h1>
        <div class="flex gap-2">
            <a href="{{ route('client.export') }}" 
                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-xs shadow">
                ⬇️ Export
            </a>
            <a href="{{ route('client.create') }}" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs shadow">
                + Add Client
            </a>
        </div>
    </div>

    <!-- Import Form -->
    <form action="{{ route('client.import') }}" method="POST" enctype="multipart/form-data"
        class="bg-white p-3 border border-gray-200 rounded-md shadow-sm flex flex-col sm:flex-row gap-3 sm:items-center">
        @csrf
        <label class="text-xs font-medium text-gray-700">📥 Import Excel:</label>
        <input type="file" name="file" required
            class="text-xs border border-gray-300 rounded-md px-2 py-1 w-full sm:w-auto file:bg-blue-600 file:text-white file:px-3 file:py-1.5 file:rounded-md">
        <button type="submit"
            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-xs shadow">
            Import
        </button>
    </form>

    <!-- Table -->
    <div class="bg-white p-2 shadow-md rounded-md border border-gray-100 overflow-x-auto relative">
        @livewire('client-table')
    </div>

    <!-- Loading Bar -->
    <div wire:loading class="fixed top-0 left-0 right-0 bg-blue-500 text-white py-1 text-xs text-center z-50">
        Loading...
    </div>
</div>

<style>
    /* Sidebar Lebih Sempit */
    .sidebar { width: 200px !important; }

    /* Header Padat */
    header { padding: 0.4rem 1rem !important; min-height: 48px; }

    /* Table Lebih Padat */
    table td, table th {
        padding: 4px 6px !important;
        font-size: 12px !important;
        white-space: nowrap;
    }

    /* Konten Padat */
    main { padding: 6px !important; }
</style>
@endsection