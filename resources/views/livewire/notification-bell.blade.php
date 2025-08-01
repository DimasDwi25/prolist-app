<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative focus:outline-none">
        <span class="text-xl">🔔</span>
        @if($this->notifications->count())
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full shadow">
                {{ $this->notifications->count() }}
            </span>
        @endif
    </button>

    <div x-show="open" @click.away="open=false" x-transition
        class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50">
        <div class="p-3 border-b flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-700">Notifications</h3>
            @if($this->notifications->count())
                <button wire:click="markAllAsRead" class="text-xs text-blue-500 hover:underline">
                    Mark all as read
                </button>
            @endif
        </div>
        <ul class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
            @forelse($this->notifications as $notification)
                <li class="p-3 hover:bg-gray-50 flex justify-between items-center">
                    <a href="#"
                        wire:click.prevent="openApproval({{ $notification->data['phc_id'] ?? 'null' }}, '{{ $notification->id }}')"
                        class="text-sm text-gray-800">
                        {{ $notification->data['message'] ?? 'New Notification' }}
                    </a>
                    <button wire:click="markAsRead('{{ $notification->id }}')"
                        class="text-xs text-gray-500 hover:text-red-500">
                        Mark
                    </button>
                </li>
            @empty
                <li class="p-3 text-gray-500 text-sm text-center">
                    No notifications
                </li>
            @endforelse
        </ul>
    </div>
</div>