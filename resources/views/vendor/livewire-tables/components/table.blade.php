@aware(['tableName','isTailwind','isBootstrap'])

@php
    $customAttributes = [
        'wrapper' => $this->getTableWrapperAttributes(),
        'table' => $this->getTableAttributes(),
        'thead' => $this->getTheadAttributes(),
        'tbody' => $this->getTbodyAttributes(),
    ];
@endphp

@if ($isTailwind)
    <div class="w-full">
        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-auto rounded-xl border border-gray-200 bg-white shadow-sm relative overflow-x-auto pb-2 max-w-screen-xl">
            <table
                wire:key="{{ $tableName }}-table"
                {{ $attributes->merge($customAttributes['table'])
                    ->class(['min-w-full divide-y divide-gray-100 text-sm text-gray-800 bg-white' => $customAttributes['table']['default'] ?? true])
                    ->except(['default','default-styling','default-colors']) }}
            >
                <thead wire:key="{{ $tableName }}-thead"
                    {{ $attributes->merge($customAttributes['thead'])
                        ->class(['bg-gray-100 text-xs text-gray-500 uppercase tracking-wider' => $customAttributes['thead']['default'] ?? true])
                        ->except(['default','default-styling','default-colors']) }}
                >
                    <tr>
                        {{ $thead }}
                    </tr>
                </thead>

                <tbody
                    wire:key="{{ $tableName }}-tbody"
                    id="{{ $tableName }}-tbody"
                    {{ $attributes->merge($customAttributes['tbody'])
                            ->class(['bg-white divide-y divide-gray-100' => $customAttributes['tbody']['default'] ?? true])
                            ->except(['default','default-styling','default-colors']) }}
                >
                    {{ $slot }}
                </tbody>

                @isset($tfoot)
                    <tfoot wire:key="{{ $tableName }}-tfoot">
                        {{ $tfoot }}
                    </tfoot>
                @endisset
            </table>
        </div>

        {{-- Mobile Stack --}}
        {{-- Mobile Stack (Modern UI) --}}
        <div class="md:hidden space-y-4">
            @foreach ($this->getRows() as $rowIndex => $row)
                <div class="rounded-xl border border-gray-200 shadow hover:shadow-md transition bg-white p-4">
                    <div class="space-y-2">
                        @foreach ($this->getColumns() as $column)
                            @if ($column->isVisible())
                                <div class="flex justify-between items-start text-sm">
                                    <div class="text-gray-500 font-medium w-1/3">
                                        {{ $column->getTitle() }}
                                    </div>
                                    <div class="text-gray-800 w-2/3 text-right break-words">
                                        {!! $column->renderContents($row) !!}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@endif

