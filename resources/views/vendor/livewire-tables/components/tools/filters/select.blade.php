<div>
    <x-livewire-tables::tools.filter-label
        :$filter
        :$filterLayout
        :$tableName
        :$isTailwind
        :$isBootstrap4
        :$isBootstrap5
        :$isBootstrap
    />

    @php
        $classes = collect([
            'block w-full transition duration-150 ease-in-out rounded-md shadow-sm focus:ring focus:ring-opacity-50' =>
                $isTailwind && ($filterInputAttributes['default-styling'] ?? true),
            'border-gray-300 focus:border-indigo-300 focus:ring-indigo-200' =>
                $isTailwind && ($filterInputAttributes['default-colors'] ?? true),
            'form-control' =>
                $isBootstrap4 && ($filterInputAttributes['default-styling'] ?? true),
            'form-select' =>
                $isBootstrap5 && ($filterInputAttributes['default-styling'] ?? true),
        ])
        ->filter()
        ->keys()
        ->implode(' ');
    @endphp

    <div @class([
        'rounded-md shadow-sm' => $isTailwind,
        'inline' => $isBootstrap,
    ])>
        <select
            {!! $filter->getWireMethod('filterComponents.' . $filter->getKey()) !!}
            class="{{ $classes ?: ' ' }}"
            {{ $filterInputAttributes->except(['default-styling', 'default-colors'])->toHtml() }}
        >
            @foreach($filter->getOptions() as $key => $value)
                @if (is_iterable($value))
                    <optgroup label="{{ $key }}">
                        @foreach ($value as $optionKey => $optionValue)
                            <option value="{{ $optionKey }}">{{ $optionValue }}</option>
                        @endforeach
                    </optgroup>
                @else
                    <option value="{{ $key }}">{{ $value }}</option>
                @endif
            @endforeach
        </select>
    </div>
</div>