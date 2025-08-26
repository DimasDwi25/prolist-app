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
        <h1 class="text-2xl font-bold text-gray-800">📋 Quotation List</h1>
        <a href="{{ route('quotation.create') }}"
            class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow transition">
            + Add Quotation
        </a>
    </div>

    <div class="flex flex-col md:flex-row justify-between gap-2 mb-4">
        <a href="{{ route('quotation.export') }}"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm shadow">
            ⬇️ Export Quotation
        </a>

        <form action="{{ route('quotation.import') }}" method="POST" enctype="multipart/form-data" class="flex gap-2">
            @csrf
            <input type="file" name="file" required class="border rounded px-2 py-1 text-sm">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">📥 Import</button>
        </form>
    </div>


    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-md bg-green-100 text-green-800 font-medium shadow">
            {{ session('success') }}
        </div>
    @endif

    <!-- Main Table Container with Loading State -->
    <div class="relative bg-white p-4 shadow-md rounded-xl border border-gray-100 over" x-data="{ loading: false }" x-init="
                    Livewire.on('loadingStarted', () => { loading = true });
                    Livewire.on('loadingFinished', () => { loading = false });
                ">

        <!-- Table Component -->
        <div wire:loading.class="opacity-50" 
            wire:loading.class.remove="opacity-100"
            wire:target="selectedYear,selectedMonth,search,filters" 
            data-turbo="false">

            <!-- Tambahkan wrapper scroll -->
            <div class="overflow-x-auto w-full">
                <livewire:supervisor-marketing.quotation-table />
            </div>
        </div>


        <livewire:supervisor-marketing.change-quotation-status-modal />
    </div>

    <!-- Additional Styling -->
    <style>
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

        .table-container {
            transition: all 0.3s ease-in-out;
        }

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
@endsection