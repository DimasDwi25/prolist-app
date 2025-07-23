<div class="space-y-4">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between gap-4 items-center">
        <h3 class="text-lg font-bold text-gray-700 flex items-center gap-2">
            📋 Project Logs
        </h3>
        <div class="flex gap-2 w-full sm:w-auto">
            <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded shadow">
                + Add Log
            </button>
            {{ $search }} {{-- search bawaan --}}
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
        {{ $table }}
    </div>

    {{-- Pagination --}}
    <div class="flex justify-between items-center mt-4 text-sm text-gray-600">
        <div>{{ $paginationInfo }}</div>
        <div>{{ $paginationLinks }}</div>
    </div>
</div>