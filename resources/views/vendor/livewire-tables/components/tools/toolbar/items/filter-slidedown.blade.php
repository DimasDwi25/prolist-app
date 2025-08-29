@aware(['tableName', 'isTailwind', 'isBootstrap'])

<div x-cloak x-show="filtersOpen" 
    {{ $attributes
        ->merge($this->getFilterSlidedownWrapperAttributes)
        ->merge($isTailwind ? [
            'x-transition:enter' => 'transition ease-out duration-100',
            'x-transition:enter-start' => 'transform opacity-0',
            'x-transition:enter-end' => 'transform opacity-100',
            'x-transition:leave' => 'transition ease-in duration-75',
            'x-transition:leave-start' => 'transform opacity-100',
            'x-transition:leave-end' => 'transform opacity-0',
        ] : [])
        ->class(['container' => $isBootstrap && ($this->getFilterSlidedownWrapperAttributes['default'] ?? true)])
        ->except(['default','default-colors','default-styling'])
    }}
>
    @foreach($this->getFiltersByRow() as $rowIndex => $filters)
        @php($rowAttrs = $this->getFilterSlidedownRowAttributes($rowIndex))
        <div {{ $attributes->merge($rowAttrs)->merge(['row' => $rowIndex])
                ->class([
                    'row col-12' => $isBootstrap && ($rowAttrs['default-styling'] ?? true),
                    'grid grid-cols-12 gap-6 px-4 py-2 mb-2' => $isTailwind && ($rowAttrs['default-styling'] ?? true),
                ])
                ->except(['default','default-colors','default-styling'])
            }}
        >
            @foreach($filters as $filter)
                @php($span = $filter->getFilterSlidedownColspan())
                <div id="{{ $tableName }}-filter-{{ $filter->getKey() }}-wrapper"
                    @class([
                        // Bootstrap
                        'space-y-1 mb-4' => $isBootstrap,
                        'col-12 col-sm-9 col-md-6 col-lg-3' => $isBootstrap && !$filter->hasFilterSlidedownColspan(),
                        'col-12 col-sm-6 col-md-6 col-lg-3' => $isBootstrap && $span === 2,
                        'col-12 col-sm-3 col-md-3 col-lg-3' => $isBootstrap && $span === 3,
                        'col-12 col-sm-1 col-md-1 col-lg-1' => $isBootstrap && $span === 4,

                        // Tailwind
                        'space-y-1 col-span-12' => $isTailwind,
                        'sm:col-span-6 md:col-span-4 lg:col-span-2' => $isTailwind && !$filter->hasFilterSlidedownColspan(),
                        'sm:col-span-12 md:col-span-8 lg:col-span-4' => $isTailwind && $span === 2,
                        'sm:col-span-9 md:col-span-4 lg:col-span-3' => $isTailwind && $span === 3,
                    ])
                >
                    {{ $filter->setGenericDisplayData($this->getFilterGenericData)->render() }}
                </div>
            @endforeach
        </div>
    @endforeach
</div>
