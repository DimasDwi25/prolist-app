@extends('supervisor.layouts.app')

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

    <!-- Info Box untuk memberitahu user bahwa perlu filter -->
    <div class="mb-4 px-4 py-3 rounded-md bg-blue-50 border border-blue-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-800">
                    <strong>💡 Info:</strong> Gunakan filter (Status, Year, Month, Quick Range) atau search untuk
                    menampilkan data quotation.
                    Data akan muncul setelah Anda memilih filter atau melakukan pencarian.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Table Container with Loading State -->
    <div class="relative bg-white p-4 shadow-md rounded-xl border border-gray-100" x-data="{ loading: false }" x-init="
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">🔄 Loading Filtered Data...</h3>
                <p class="text-sm text-gray-500">Please wait while we process your request</p>
            </div>
        </div>

        <!-- Table Component -->
        <div wire:loading.class="opacity-50" wire:loading.class.remove="opacity-100"
            wire:target="selectedYear,selectedMonth,search,filters" data-turbo="false">
            <livewire:supervisor-marketing.quotation-table />
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