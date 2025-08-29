@aware(['tableName','isTailwind','isBootstrap','isBootstrap4','isBootstrap5','localisationPath'])

@php
    $enabled = $this->sortingPillsAreEnabled() && $this->hasSorts();
    $badgeClass = $isTailwind
        ? 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 capitalize'
        : ($isBootstrap4
            ? 'badge badge-pill badge-info d-inline-flex align-items-center'
            : 'badge rounded-pill bg-info d-inline-flex align-items-center');

    $removeBtnClass = $isTailwind
        ? 'flex-shrink-0 ml-0.5 h-4 w-4 rounded-full inline-flex items-center justify-center focus:outline-none'
        : ($isBootstrap4 ? 'text-white ml-2' : 'text-white ms-2');

    $clearAllClass = $isTailwind
        ? 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium'
        : ($isBootstrap4 ? 'badge badge-pill badge-light' : 'badge rounded-pill bg-light text-dark text-decoration-none');
@endphp

@if($enabled)
    <div class="{{ $isTailwind ? 'mb-4 px-4 md:p-0' : 'mb-3' }}" x-cloak x-show="!currentlyReorderingStatus">
        @unless($isTailwind)
            <small>{{ __($localisationPath.'Applied Sorting') }}:</small>
        @endunless

        @foreach($this->getSorts() as $columnSelectName => $direction)
            @php($column = $this->getColumnBySelectName($columnSelectName) ?? $this->getColumnBySlug($columnSelectName))
            @continue(!$column || $column->isHidden() || ($this->columnSelectIsEnabled && !$this->columnSelectIsEnabledForColumn($column)))

            <span wire:key="{{ $tableName }}-sorting-pill-{{ $columnSelectName }}"
                {{ $attributes->merge($this->getSortingPillsItemAttributes())
                    ->class([$badgeClass => $this->getSortingPillsItemAttributes()['default-styling']])
                    ->except(['default-styling','default-colors']) }}
            >
                {{ $column->getSortingPillTitle() }}: {{ $column->getSortingPillDirectionLabel($direction, $this->getDefaultSortingLabelAsc, $this->getDefaultSortingLabelDesc) }}

                <a href="#" wire:click="clearSort('{{ $columnSelectName }}')"
                    {{ $attributes->merge($this->getSortingPillsClearSortButtonAttributes())
                        ->class([$removeBtnClass => $this->getSortingPillsClearSortButtonAttributes()['default-styling']])
                        ->except(['default-styling','default-colors']) }}
                >
                    <span class="{{ $isTailwind ? 'sr-only' : ($isBootstrap5 ? 'visually-hidden' : 'sr-only') }}">
                        {{ __($localisationPath.'Remove sort option') }}
                    </span>
                    <x-heroicon-m-x-mark class="{{ $isTailwind ? 'h-3 w-3' : 'laravel-livewire-tables-btn-smaller' }}" />
                </a>
            </span>
        @endforeach

        <a href="#" wire:click.prevent="clearSorts" class="focus:outline-none active:outline-none"
            {{ $attributes->merge($this->getSortingPillsClearAllButtonAttributes())
                ->class([$clearAllClass => $this->getSortingPillsClearAllButtonAttributes()['default-styling']])
                ->except(['default-styling','default-colors']) }}
        >
            {{ __($localisationPath.'Clear') }}
        </a>
    </div>
@endif
