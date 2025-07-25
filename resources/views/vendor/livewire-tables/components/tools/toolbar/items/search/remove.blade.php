@aware(['isTailwind', 'isBootstrap'])

<div @class([
    'd-inline-flex h-100 align-items-center' => $isBootstrap,
])>
    <div
        wire:click="clearSearch"
        @class([
            // Bootstrap Style
            'btn btn-outline-secondary d-inline-flex h-100 align-items-center' => $isBootstrap,

            // Tailwind Style
            'inline-flex items-center px-3 h-full text-gray-500 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md cursor-pointer sm:text-sm' => $isTailwind,
        ])
    >
        @if ($isTailwind)
            <x-heroicon-m-x-mark class="w-4 h-4" />
        @else
            <x-heroicon-m-x-mark class="laravel-livewire-tables-btn-smaller" />
        @endif
    </div>
</div>
