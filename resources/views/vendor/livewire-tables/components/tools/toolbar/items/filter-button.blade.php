@aware(['tableName','isTailwind','isBootstrap','isBootstrap4','isBootstrap5','localisationPath'])
@props([])

<div @class([
    'ml-0 ml-md-2 mb-3 mb-md-0' => $isBootstrap4,
    'ms-0 ms-md-2 mb-3 mb-md-0' => $isBootstrap5 && $this->searchIsEnabled(),
    'mb-3 mb-md-0' => $isBootstrap5 && !$this->searchIsEnabled(),
])>
    <div 
        @if($this->isFilterLayoutPopover())
            x-data="{ filterPopoverOpen: false }" 
            x-on:keydown.escape.stop="if (!this.childElementOpen) filterPopoverOpen = false" 
            x-on:mousedown.away="if (!this.childElementOpen) filterPopoverOpen = false"
        @endif
        @class([
            'btn-group d-block d-md-inline' => $isBootstrap,
            'relative block md:inline-block text-left' => $isTailwind,
        ])
    >
        <button type="button" 
            @class([
                'btn dropdown-toggle d-block w-100 d-md-inline' => $isBootstrap,
                'inline-flex items-center justify-center gap-2 w-full rounded-md border border-gray-300 bg-white text-gray-800 text-sm font-medium px-4 py-2 shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition' => $isTailwind,
            ])
            @if($this->isFilterLayoutPopover())
                x-on:click="filterPopoverOpen = !filterPopoverOpen" aria-haspopup="true" x-bind:aria-expanded="filterPopoverOpen"
            @elseif($this->isFilterLayoutSlideDown())
                x-on:click="filtersOpen = !filtersOpen"
            @endif
        >
            <span>{{ __($localisationPath.'Filters') }}</span>
            @if($count = $this->getFilterBadgeCount())
                <span @class([
                    'badge badge-info' => $isBootstrap,
                    'bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded-full' => $isTailwind,
                ])>{{ $count }}</span>
            @endif
            @if($isTailwind)
                <x-heroicon-o-funnel class="w-4 h-4 text-gray-500" />
            @else
                <span class="caret"></span>
            @endif
        </button>
        @if($this->isFilterLayoutPopover())
            <x-livewire-tables::tools.toolbar.items.filter-popover />
        @endif
    </div>
</div>
