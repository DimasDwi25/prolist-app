<div class="max-w-5xl mx-auto space-y-6">

    @if (session()->has('success'))
        <div class="p-3 bg-green-100 border border-green-400 text-green-800 rounded shadow">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form -->
    <form wire:submit.prevent="save" class="space-y-4 bg-white p-6 rounded-xl shadow border">
        <h2 class="text-2xl font-semibold mb-2 text-gray-800">
            {{ $isEditing ? '✏️ Edit Scope of Work' : '➕ Add Scope of Work' }}
        </h2>

        <div>
            <label class="block font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
            <input type="text" wire:model.defer="names"
                class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200 focus:outline-none">
            @error('names') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block font-medium text-gray-700">Description</label>
            <textarea wire:model.defer="description"
                class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-blue-200 focus:outline-none"
                rows="3"></textarea>
            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex space-x-2">
            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                {{ $isEditing ? 'Update' : 'Save' }}
            </button>
            @if($isEditing)
                <button type="button" wire:click="resetForm" class="text-gray-600 hover:text-black underline">
                    Cancel
                </button>
            @endif
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow border p-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">📋 List Scope of Work</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="py-2 px-4 text-left">Name</th>
                        <th class="py-2 px-4 text-left">Description</th>
                        <th class="py-2 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($scopeOfWorks as $sow)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="py-2 px-4">{{ $sow->names }}</td>
                            <td class="py-2 px-4">{{ $sow->description }}</td>
                            <td class="py-2 px-4 text-right space-x-2">
                                <button wire:click="edit({{ $sow->id }})"
                                    class="text-blue-600 hover:underline">Edit</button>
                                <button wire:click="delete({{ $sow->id }})" onclick="return confirm('Are you sure?')"
                                    class="text-red-600 hover:underline">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">No Scope of Work found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $scopeOfWorks->links() }}
        </div>
    </div>
</div>