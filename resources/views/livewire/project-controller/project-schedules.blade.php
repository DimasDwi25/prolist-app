<div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Project Schedule</h2>
        <input type="text" wire:model.debounce.300ms="search" placeholder="Search by Plan Date..."
            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
    </div>

    {{-- Form --}}
    <form wire:submit.prevent="save" class="space-y-4 mb-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 text-sm mb-1">Bobot (%)</label>
                <input type="number" step="0.01" wire:model="bobot" class="w-full border rounded p-2">
                @error('bobot') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-700 text-sm mb-1">Plan Start</label>
                <input type="date" wire:model="plan_start" class="w-full border rounded p-2">
                @error('plan_start') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 text-sm mb-1">Plan Finish</label>
                <input type="date" wire:model="plan_finish" class="w-full border rounded p-2">
                @error('plan_finish') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-700 text-sm mb-1">Actual Start</label>
                <input type="date" wire:model="actual_date" class="w-full border rounded p-2">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 text-sm mb-1">Actual Finish</label>
                <input type="date" wire:model="actual_finish" class="w-full border rounded p-2">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded w-full">
                    {{ $isEdit ? 'Update Schedule' : 'Add Schedule' }}
                </button>
                @if($isEdit)
                    <button type="button" wire:click="resetForm" class="bg-gray-400 text-white px-4 py-2 rounded ml-2">
                        Cancel
                    </button>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <table class="w-full border-collapse border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2 text-left">Plan Start</th>
                <th class="border p-2 text-left">Plan Finish</th>
                <th class="border p-2 text-left">Bobot</th>
                <th class="border p-2 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $schedule)
                <tr>
                    <td class="border p-2">{{ $schedule->plan_start }}</td>
                    <td class="border p-2">{{ $schedule->plan_finish }}</td>
                    <td class="border p-2">{{ $schedule->bobot }}%</td>
                    <td class="border p-2 text-center space-x-2">
                        <button wire:click="edit({{ $schedule->id }})" class="bg-yellow-400 px-3 py-1 rounded">Edit</button>
                        <button wire:click="delete({{ $schedule->id }})" class="bg-red-500 text-white px-3 py-1 rounded"
                            onclick="return confirm('Delete this schedule?')">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="border p-4 text-center text-gray-500">No schedules found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $schedules->links() }}
    </div>
</div>