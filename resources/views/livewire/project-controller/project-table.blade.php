<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-700">📋 Work Orders</h2>
        <a href="{{ route('work-order.create') }}"
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
           + Create Work Order
        </a>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        {{ $this->table }}
    </div>
</div>
