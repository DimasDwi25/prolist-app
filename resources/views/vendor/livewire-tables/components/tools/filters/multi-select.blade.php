@php
    $filterKey = $filter->getKey();
    $pos = $filter->hasCustomPosition() ? '-'.$filter->getCustomPosition() : '';
    $baseId = "{$tableName}-filter-{$filterKey}";
@endphp

<div>
    <x-livewire-tables::tools.filter-label :$filter :$filterLayout :$tableName :$isTailwind :$isBootstrap4 :$isBootstrap5 :$isBootstrap />

    @if($isTailwind)<div class="rounded-md shadow-sm">@endif
        
        {{-- Select All --}}
        <div @class(['form-check' => $isBootstrap])>
            <input id="{{ $baseId }}-select-all{{ $pos }}"
                   wire:input="selectAllFilterOptions('{{ $filterKey }}')"
                   {{ $filterInputAttributes->merge(['type'=>'checkbox'])
                        ->class([
                            'rounded shadow-sm transition duration-150 ease-in-out focus:ring focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-wait' => $isTailwind && ($filterInputAttributes['default-styling'] ?? true),
                            'text-indigo-600 border-gray-300 focus:border-indigo-300 focus:ring-indigo-200' => $isTailwind && ($filterInputAttributes['default-colors'] ?? true),
                            'form-check-input' => $isBootstrap && ($filterInputAttributes['default-styling'] ?? true),
                        ])
                        ->except(['id','wire:key','value','default-styling','default-colors']) 
                   }}>
            <label for="{{ $baseId }}-select-all{{ $pos }}" @class(['form-check-label'=>$isBootstrap])>
                {{ $filter->getFirstOption() !== '' ? $filter->getFirstOption() : __($localisationPath.'All') }}
            </label>
        </div>

        {{-- Filter Options --}}
        @foreach($filter->getOptions() as $key => $value)
            @php $optId = "{$baseId}-{$loop->index}{$pos}"; @endphp
            <div @class(['form-check'=>$isBootstrap]) wire:key="{{ $optId }}">
                <input {!! $filter->getWireMethod("filterComponents.$filterKey") !!}
                       id="{{ $optId }}" wire:key="{{ $optId }}" value="{{ $key }}"
                       {{ $filterInputAttributes->merge(['type'=>'checkbox'])
                            ->class([
                                'rounded shadow-sm transition duration-150 ease-in-out focus:ring focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-wait' => $isTailwind && ($filterInputAttributes['default-styling'] ?? true),
                                'text-indigo-600 border-gray-300 focus:border-indigo-300 focus:ring-indigo-200' => $isTailwind && ($filterInputAttributes['default-colors'] ?? true),
                                'form-check-input' => $isBootstrap && ($filterInputAttributes['default-styling'] ?? true),
                            ])
                            ->except(['id','wire:key','value','default-styling','default-colors'])
                       }}>
                <label for="{{ $optId }}" @class(['form-check-label'=>$isBootstrap])>{{ $value }}</label>
            </div>
        @endforeach

    @if($isTailwind)</div>@endif
</div>
