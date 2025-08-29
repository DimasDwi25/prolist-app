<div class="flex flex-col space-y-1">
    {{-- Label --}}
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
            // Tailwind Compact
            'block w-full rounded-md text-xs px-2 py-1 border border-gray-300 shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50' =>
                $isTailwind && ($filterInputAttributes['default-styling'] ?? true),
            'border-gray-300 focus:border-indigo-300' =>
                $isTailwind && ($filterInputAttributes['default-colors'] ?? true),

            // Bootstrap Compact
            'form-control form-control-sm' =>
                $isBootstrap4 && ($filterInputAttributes['default-styling'] ?? true),
            'form-select form-select-sm' =>
                $isBootstrap5 && ($filterInputAttributes['default-styling'] ?? true),
        ])
        ->filter()
        ->keys()
        ->implode(' ');
    @endphp

    {{-- Select Filter --}}
    <div @class([
        'rounded-md shadow-sm w-full' => $isTailwind,
        'inline' => $isBootstrap,
    ])>
        <select
            {!! $filter->getWireMethod('filterComponents.' . $filter->getKey()) !!}
            class="{{ $classes }}"
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
