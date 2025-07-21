@aware([ 'tableName','primaryKey','isTailwind','isBootstrap'])
@props(['row', 'rowIndex'])

@php
    $customAttributes = $this->getTrAttributes($row, $rowIndex);
@endphp

<tr
    rowpk='{{ $row->{$primaryKey} }}'
    x-on:dragstart.self="currentlyReorderingStatus && dragStart(event)"
    x-on:drop.prevent="currentlyReorderingStatus && dropEvent(event)"
    x-on:dragover.prevent.throttle.500ms="currentlyReorderingStatus && dragOverEvent(event)"
    x-on:dragleave.prevent.throttle.500ms="currentlyReorderingStatus && dragLeaveEvent(event)"
    @if($this->hasDisplayLoadingPlaceholder()) 
        wire:loading.class.add="hidden d-none"
    @else
        wire:loading.class.delay="opacity-50 bg-gray-100"
    @endif
    id="{{ $tableName }}-row-{{ $row->{$primaryKey} }}"
    :draggable="currentlyReorderingStatus"
    wire:key="{{ $tableName }}-tablerow-tr-{{ $row->{$primaryKey} }}"
    loopType="{{ ($rowIndex % 2 === 0) ? 'even' : 'odd' }}"
    {{
        $attributes->merge($customAttributes)
            ->class([
                // Ubah warna latar baris agar terang dan konsisten
                'bg-white' => $isTailwind && $rowIndex % 2 === 0 && ($customAttributes['default'] ?? true),
                'bg-gray-50' => $isTailwind && $rowIndex % 2 !== 0 && ($customAttributes['default'] ?? true),

                // Tambahan efek hover & pointer
                'hover:bg-blue-50 transition-colors duration-200' => $isTailwind && ($customAttributes['default'] ?? true),
                'cursor-pointer' => $isTailwind && $this->hasTableRowUrl() && ($customAttributes['default'] ?? true),

                // Bootstrap (tidak dipakai tapi tetap aman)
                'bg-light' => $isBootstrap && $rowIndex % 2 === 0 && ($customAttributes['default'] ?? true),
            ])
            ->except(['default','default-styling','default-colors'])
    }}
>
    {{ $slot }}
</tr>
