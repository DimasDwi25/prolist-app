@aware(['tableName', 'isTailwind', 'isBootstrap', 'isBootstrap4', 'isBootstrap5', 'localisationPath'])

<div {{ $attributes
    ->merge(['wire:loading.class' => $this->displayFilterPillsWhileLoading ? '' : 'invisible', 'x-cloak'])
    ->class([
        'mb-4 px-4 md:p-0' => $isTailwind,
        'mb-3' => $isBootstrap,
    ]) }}>

    <small class="{{ $isTailwind ? 'text-gray-700' : '' }}">
        {{ __($localisationPath.'Applied Filters') }}:
    </small>

    @foreach($this->getPillDataForFilter() as $filterKey => $filterPillData)
        @includeWhen(
            $filterPillData->hasCustomPillBlade,
            $filterPillData->getCustomPillBlade(),
            ['filter' => $this->getFilterByKey($filterKey), 'filterPillData' => $filterPillData]
        )
        @unless($filterPillData->hasCustomPillBlade)
            <x-livewire-tables::filter-pill :$filterKey :$filterPillData />
        @endunless
    @endforeach

    <x-livewire-tables::tools.filter-pills.buttons.reset-all />
</div>
