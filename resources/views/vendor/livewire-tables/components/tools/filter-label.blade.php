@aware(['tableName'])
@props([
    'filter',
    'filterLayout' => 'popover',
    'tableName' => 'table',
    'isTailwind' => false,
    'isBootstrap' => false,
    'isBootstrap4' => false,
    'isBootstrap5' => false,
    'for' => null,
])

@php
    $filterLabelAttrs = $filter->getFilterLabelAttributes();
    $customLabelAttrs = $filter->getLabelAttributes();
    $defaultStyling = $filterLabelAttrs['default-styling'] ?? ($filterLabelAttrs['default'] ?? true);
    $labelId = $for ?? "{$tableName}-filter-{$filter->getKey()}";
@endphp

@if ($filter->hasCustomFilterLabel() && !$filter->hasCustomPosition())
    @include($filter->getCustomFilterLabel(), [
        'filter' => $filter,
        'filterLayout' => $filterLayout,
        'tableName' => $tableName,
        'isTailwind' => $isTailwind,
        'isBootstrap' => $isBootstrap,
        'isBootstrap4' => $isBootstrap4,
        'isBootstrap5' => $isBootstrap5,
        'customLabelAttributes' => $customLabelAttrs
    ])
@elseif(!$filter->hasCustomPosition())
    <label for="{{ $labelId }}"
        {{
            $attributes->merge($customLabelAttrs)
                ->merge($filterLabelAttrs)
                ->class([
                    // Tailwind style (compact)
                    'block text-xs font-medium leading-4 text-gray-600 mb-1' =>
                        $isTailwind && $defaultStyling,

                    // Bootstrap style (compact)
                    'd-block small text-muted mb-1' =>
                        $isBootstrap && $filterLayout === 'slide-down' && $defaultStyling,
                    'mb-1 small text-muted' =>
                        $isBootstrap && $filterLayout === 'popover' && $defaultStyling,
                ])
                ->except(['default', 'default-colors', 'default-styling'])
        }}
    >
        {{ $filter->getName() }}
    </label>
@endif
