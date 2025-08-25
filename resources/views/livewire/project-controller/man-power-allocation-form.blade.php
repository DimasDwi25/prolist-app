<div class="space-y-6">

    <!-- Tombol Back -->
    <div>
        <a href="{{ route('engineer.project.show', $project->pn_number) }}" 
            class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md text-sm font-medium transition">
            ← Back to Project
        </a>
    </div>

    <!-- Success Notification -->
    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded shadow-sm">
            <p class="text-emerald-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Form Section -->
    <section class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800">
                {{ $isEditing ? 'Edit Man Power Allocation' : 'Add New Man Power Allocation' }}
            </h2>
        </div>
        
        <form wire:submit.prevent="save" class="p-6 space-y-5">
        
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User <span class="text-red-500">*</span></label>
                <select wire:model.defer="user_id"
                    class="select2 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                <select wire:model.defer="role_id"
                    class="select2 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Role --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4">
                @if($isEditing)
                    <button type="button" wire:click="resetForm"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                @endif
                <button type="submit"
                    class="px-4 py-2 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    {{ $isEditing ? 'Update Allocation' : 'Save Allocation' }}
                </button>
            </div>
        </form>
    </section>

    <!-- Data Table Section -->
    <section class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Man Power Allocation List</h3>
            <input type="text" placeholder="Search by Project PN..." 
                class="pl-3 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500"
                wire:model.debounce.300ms="search">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($allocations as $alloc)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $alloc->project->pn_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $alloc->user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $alloc->role->name }}</td>
                            <td class="px-6 py-4 text-right text-sm">
                                <button wire:click="edit({{ $alloc->id }})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                <button wire:click="delete({{ $alloc->id }})" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No allocations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $allocations->links() }}
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function initSelect2() {
            $('.select2').select2({
                placeholder: 'Select an option',
                width: '100%'
            }).on('change', function (e) {
                @this.set(this.getAttribute('wire:model.defer'), $(this).val());
            });
        }

        initSelect2(); // inisialisasi pertama

        Livewire.hook('message.processed', (message, component) => {
            initSelect2(); // re-inisialisasi setelah update Livewire
        });
    });

</script>
