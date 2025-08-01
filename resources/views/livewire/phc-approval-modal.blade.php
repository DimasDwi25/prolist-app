<div class="relative" x-data="{ open: false }">
    <!-- Bell Button -->
    <button @click="open = !open"
        class="relative flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 transition">
        <!-- Bell Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14V11a6 6 0 00-9.33-4.977M5 8v6a2 2 0 002 2h1m2 4h2a2 2 0 002-2H9a2 2 0 002 2z" />
        </svg>

        <!-- Badge -->
        @if($notifications->count())
            <span
                class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow">
                {{ $notifications->count() }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="open" @click.away="open=false" x-transition
        class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50 overflow-hidden">

        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h4 class="text-sm font-semibold text-gray-700">Notifications</h4>
            @if($notifications->count())
                <button wire:click="markAllAsRead" class="text-xs text-blue-500 hover:underline">Mark all as read</button>
            @endif
        </div>

        <!-- Notifications List -->
        <ul class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <li class="p-4 hover:bg-gray-50 transition flex justify-between items-center">
                    <a href="#"
                        wire:click.prevent="openApproval({{ $notification->data['phc_id'] ?? 'null' }}, '{{ $notification->id }}')"
                        class="text-sm text-gray-800 leading-tight hover:text-blue-600 transition">
                        {{ $notification->data['message'] ?? 'New Notification' }}
                    </a>
                    <button wire:click="markAsRead('{{ $notification->id }}')"
                        class="text-[11px] text-gray-400 hover:text-red-500 transition">Mark</button>
                </li>
            @empty
                <li class="p-4 text-gray-500 text-sm text-center">No new notifications</li>
            @endforelse
        </ul>
    </div>
</div>