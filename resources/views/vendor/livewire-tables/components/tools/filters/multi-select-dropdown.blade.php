<div>
    <x-livewire-tables::tools.filter-label 
        :$filter :$filterLayout :$tableName 
        :$isTailwind :$isBootstrap4 :$isBootstrap5 :$isBootstrap 
    />

    @if ($isTailwind)
    <div class="rounded-sm">
    @endif
        <select multiple
            {!! $filter->getWireMethod('filterComponents.'.$filter->getKey()) !!} {{
                $filterInputAttributes->merge([
                    'wire:key' => $filter->generateWireKey($tableName, 'multiselectdropdown'),
                ])
                ->class([
                    // Tailwind
                    'block w-full rounded-sm border-gray-300 text-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50 focus:border-indigo-300' 
                        => $isTailwind && ($filterInputAttributes['default-styling'] ?? true),
                    'py-1 px-2' => $isTailwind,
                    'shadow-sm' => false, // Hilangkan shadow besar
                    // Bootstrap
                    'form-control form-control-sm' => $isBootstrap4 && ($filterInputAttributes['default-styling'] ?? true),
                    'form-select form-select-sm' => $isBootstrap5 && ($filterInputAttributes['default-styling'] ?? true),
                ])
                ->except(['default-styling','default-colors']) 
            }}>
            @if ($filter->getFirstOption() !== '')
                <option @if($filter->isEmpty($this)) selected @endif value="all">
                    {{ $filter->getFirstOption() }}
                </option>
            @endif
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
    @if ($isTailwind)
    </div>
    @endif
</div>
