<div class="space-y-1">
    <x-livewire-tables::tools.filter-label 
        :$filter 
        :$filterLayout 
        :$tableName 
        :$isTailwind 
        :$isBootstrap4 
        :$isBootstrap5 
        :$isBootstrap 
        class="text-xs font-medium text-gray-600"
    />

    <div @class([
        'rounded shadow-sm' => $isTailwind,
        'mb-2 mb-md-0 input-group input-group-sm' => $isBootstrap,
    ])>
        <input {!! $filter->getWireMethod('filterComponents.'.$filter->getKey()) !!} {{
            $filterInputAttributes->merge()
            ->class([
                // Tailwind Style
                'block w-full text-xs rounded-md shadow-sm px-2 py-1 transition duration-150 ease-in-out focus:ring focus:ring-opacity-50' => $isTailwind && ($filterInputAttributes['default-styling'] ?? true),
                'border-gray-300 focus:border-indigo-300 focus:ring-indigo-200' => $isTailwind && ($filterInputAttributes['default-colors'] ?? true),

                // Bootstrap Style
                'form-control form-control-sm' => $isBootstrap,
            ])
            ->except(['default-styling','default-colors'])
        }} />
    </div>
</div>
