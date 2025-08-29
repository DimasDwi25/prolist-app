@aware(['tableName','isTailwind','isBootstrap4','isBootstrap5'])
@props([
    'filterKey', 
    'filterPillData', 
    'shouldWatch' => ($filterPillData->shouldWatchForEvents() ?? 0),
    'filterPillsItemAttributes' => $filterPillData->getFilterPillsItemAttributes(),
])

<div 
    x-data="filterPillsHandler(@js($filterPillData->getPillSetupData($filterKey,$shouldWatch)))" 
    x-bind="trigger" 
    wire:key="{{ $tableName }}-filter-pill-{{ $filterKey }}" 
    {{
        $attributes->merge($filterPillsItemAttributes)
        ->class([
            // Tailwind versi compact
            'inline-flex items-center px-1.5 py-0.5 rounded-md leading-3 text-xs space-x-1' 
                => $isTailwind && ($filterPillsItemAttributes['default-styling'] ?? true),
            'bg-indigo-100 text-indigo-700 font-medium' 
                => $isTailwind && ($filterPillsItemAttributes['default-colors'] ?? true),

            // Bootstrap 4 & 5 versi compact
            'badge badge-pill badge-info d-inline-flex align-items-center py-0 px-1 small' 
                => $isBootstrap4 && ($filterPillsItemAttributes['default-styling'] ?? true),
            'badge rounded-pill bg-info d-inline-flex align-items-center py-0 px-1 small' 
                => $isBootstrap5 && ($filterPillsItemAttributes['default-styling'] ?? true),
        ])
        ->except(['default', 'default-styling', 'default-colors'])
    }}
>
    <span class="whitespace-nowrap" x-text="localFilterTitle + ':'"></span>
    <span {{ $filterPillData->getFilterPillDisplayData() }} class="whitespace-nowrap"></span>

    <x-livewire-tables::tools.filter-pills.buttons.reset-filter 
        :$filterKey 
        :$filterPillData
        class="ml-1"
    />
</div>
