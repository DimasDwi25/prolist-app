<div class="max-w-6xl mx-auto bg-white p-8 rounded-2xl shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">📋 Master Task List</h2>

    </div>

    {{-- Flash message --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-700 border border-green-200">
            {{ session('message') }}
        </div>
    @endif

    {{-- Form Input --}}
    <form wire:submit.prevent="save" class="space-y-4 mb-8">
        <div>
            <label class="block text-gray-600 font-medium mb-1">Task Name</label>
            <input type="text" wire:model="taskName"
                class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                placeholder="Enter task name">
            @error('taskName')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-gray-600 font-medium mb-1">Description</label>
            <textarea wire:model="description"
                class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                placeholder="Enter description (optional)"></textarea>
            @error('description')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition duration-200 ease-in-out">
                {{ $isEdit ? 'Update Task' : 'Add Task' }}
            </button>
            @if ($isEdit)
                <button type="button" wire:click="resetForm"
                    class="px-5 py-2.5 bg-gray-400 hover:bg-gray-500 text-white rounded-lg shadow transition duration-200 ease-in-out">
                    Cancel
                </button>
            @endif
        </div>
    </form>

    {{-- Task List --}}
    <div class="overflow-x-auto">
        {{-- Search Box --}}
        <input type="text" wire:model.debounce.300ms="search" placeholder="Search task..."
            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 w-64 mb-5">
        <table class="min-w-full text-sm text-left text-gray-700 border border-gray-200 rounded-lg shadow">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-4 py-3 border-b">Task Name</th>
                    <th class="px-4 py-3 border-b">Description</th>
                    <th class="px-4 py-3 border-b text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($tasks as $task)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">{{ $task->task_name }}</td>
                        <td class="px-4 py-3">{{ $task->description ?? '-' }}</td>
                        <td class="px-4 py-3 text-center space-x-2">
                            <button wire:click="edit({{ $task->id }})"
                                class="px-3 py-1 bg-yellow-400 hover:bg-yellow-500 text-gray-800 text-sm rounded shadow transition">
                                Edit
                            </button>
                            <button wire:click="delete({{ $task->id }})"
                                class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded shadow transition"
                                onclick="return confirm('Delete this task?')">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500">No tasks found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $tasks->links() }}
    </div>
</div>