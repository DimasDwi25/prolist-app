@aware(['tableName','isTailwind','isBootstrap'])
@php
    $toolBar = $this->getToolBarAttributesBag;
    $defaultStyling = $toolBar['default-styling'] ?? true;
@endphp

<div {{
    $toolBar
        ->merge()
        ->class([
            'md:flex md:justify-between mb-4 px-4 md:p-0' => $isTailwind && $defaultStyling,
            'd-md-flex justify-content-between mb-3' => $isBootstrap && $defaultStyling,
        ])
        ->except(['default','default-styling','default-colors'])
}}>
    {{-- Left Toolbar --}}
    <div @class([
        'd-md-flex' => $isBootstrap,
        'w-full mb-4 md:mb-0 md:w-2/4 md:flex space-y-4 md:space-y-0 md:space-x-2' => $isTailwind,
    ])>
        @foreach (['toolbar-left-start', 'toolbar-left-end'] as $pos)
            @if ($this->hasConfigurableAreaFor($pos))
                <div x-cloak x-show="!currentlyReorderingStatus" @class([
                    'mb-3 mb-md-0 input-group' => $isBootstrap,
                    'flex rounded-md shadow-sm' => $isTailwind,
                ])>
                    @include($this->getConfigurableAreaFor($pos), $this->getParametersForConfigurableArea($pos))
                </div>
            @endif
        @endforeach

        @if ($this->showReorderButton()) <x-livewire-tables::tools.toolbar.items.reorder-buttons /> @endif
        @if ($this->showSearchField()) <x-livewire-tables::tools.toolbar.items.search-field /> @endif
        @if ($this->showFiltersButton()) <x-livewire-tables::tools.toolbar.items.filter-button /> @endif
        @if ($this->showActionsInToolbarLeft()) <x-livewire-tables::includes.actions/> @endif
    </div>

    {{-- Right Toolbar --}}
    <div x-cloak x-show="!currentlyReorderingStatus" @class([
        'd-md-flex' => $isBootstrap,
        'md:flex md:items-center space-y-4 md:space-y-0 md:space-x-2' => $isTailwind,
    ])>
        @includeWhen($this->hasConfigurableAreaFor('toolbar-right-start'), $this->getConfigurableAreaFor('toolbar-right-start'), $this->getParametersForConfigurableArea('toolbar-right-start'))
        @if ($this->showActionsInToolbarRight()) <x-livewire-tables::includes.actions/> @endif
        @if ($this->showBulkActionsDropdownAlpine() && $this->shouldAlwaysHideBulkActionsDropdownOption != true) 
            <x-livewire-tables::tools.toolbar.items.bulk-actions /> 
        @endif
        @if ($this->columnSelectIsEnabled) <x-livewire-tables::tools.toolbar.items.column-select /> @endif
        @if ($this->showPaginationDropdown()) <x-livewire-tables::tools.toolbar.items.pagination-dropdown /> @endif
        @includeWhen($this->hasConfigurableAreaFor('toolbar-right-end'), $this->getConfigurableAreaFor('toolbar-right-end'), $this->getParametersForConfigurableArea('toolbar-right-end'))
    </div>
</div>
