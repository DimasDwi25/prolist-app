<div class="max-w-6xl mx-auto space-y-4">
    <!-- Notification -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 2500)"
             class="p-3 bg-green-50 border-l-4 border-green-500 rounded-md text-sm shadow-sm">
            <div class="flex items-center">
                <svg class="h-4 w-4 text-green-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="ml-2 text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800 flex items-center">
                @if($isEditing)
                    <svg class="w-4 h-4 mr-1.5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Status
                @else
                    <svg class="w-4 h-4 mr-1.5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Status
                @endif
            </h2>
        </div>
        
        <form wire:submit.prevent="save" class="p-4 space-y-4 text-sm">
            <div>
                <label class="block text-gray-700 mb-1 font-medium">Status Name <span class="text-red-500">*</span></label>
                <input type="text" wire:model.defer="name"
                    class="w-full px-3 py-2 rounded-md border border-gray-300 focus:ring-1 focus:ring-blue-200 focus:border-blue-500 text-sm">
                @error('name') 
                    <p class="mt-1 text-xs text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex justify-end gap-2 pt-2">
                @if($isEditing)
                    <button type="button" wire:click="resetForm" 
                        class="px-3 py-1.5 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">
                        Cancel
                    </button>
                @endif
                <button type="submit"
                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 rounded-md text-white text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    {{ $isEditing ? 'Update' : 'Save' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-800 flex items-center">
                <svg class="w-4 h-4 mr-1 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Status Projects
            </h3>
            <div class="relative">
                <input type="text" placeholder="Search..." 
                    class="pl-8 pr-3 py-1.5 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-200 focus:border-blue-500 text-xs">
                <svg class="w-4 h-4 absolute left-2 top-2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4-4m0 0a7 7 0 1110 10z" />
                </svg>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">#</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Name</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Created</th>
                        <th class="px-3 py-2 text-right font-medium text-gray-500 uppercase tracking-wider text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($statusProjects as $status)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap">{{ $status->id }}</td>
                            <td class="px-3 py-2 whitespace-nowrap font-medium">{{ $status->name ?? '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-500">
                                {{ $status->created_at?->format('M d, Y') ?? '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-2">
                                    <button wire:click="edit({{ $status->id }})" 
                                        class="text-blue-600 hover:text-blue-800 text-xs flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $status->id }})"
                                        onclick="return confirm('Delete this status?')"
                                        class="text-red-600 hover:text-red-800 text-xs flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-6 text-center text-gray-500 text-sm">
                                No status projects found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($statusProjects->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $statusProjects->links() }}
            </div>
        @endif
    </div>
</div>
