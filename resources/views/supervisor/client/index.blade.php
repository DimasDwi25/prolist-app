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
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-3">
        <h1 class="text-2xl font-bold text-gray-800">📋 Client List</h1>
        <a href="{{ route('client.export') }}"
            class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow transition">
            ⬇️ Export Excel
        </a>

        <a href="{{ route('client.create') }}"
            class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow transition">
            + Add Client
        </a>
    </div>

    {{-- Import Form --}}
    <form action="{{ route('client.import') }}" method="POST" enctype="multipart/form-data"
        class="bg-white p-4 border border-gray-200 rounded-xl shadow-sm mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
        @csrf
        <label class="text-sm text-gray-700 font-medium">📥 Import Excel:</label>
        <input type="file" name="file" required
            class="block text-sm file:bg-blue-600 file:text-white file:px-4 file:py-2 file:rounded-md file:font-semibold file:border-0 file:cursor-pointer border border-gray-300 rounded-md w-full sm:w-auto">
        <button type="submit"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow transition w-full sm:w-auto">
            Import
        </button>
    </form>

    {{-- Table --}}
    <div class="bg-white p-4 shadow-md rounded-xl border border-gray-100 overflow-x-auto">
        @livewire('client-table')
    </div>
    <div wire:loading class="fixed top-0 left-0 right-0 bg-blue-500 text-white py-2 text-center z-50">
        Loading...
    </div>
@endsection