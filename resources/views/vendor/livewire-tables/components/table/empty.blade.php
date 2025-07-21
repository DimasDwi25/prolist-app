@aware(['isTailwind','isBootstrap'])

@php($attributes = $attributes->merge(['wire:key' => 'empty-message-'.$this->getId()]))

@if ($isTailwind)
    <tr {{ $attributes }}>
        <td colspan="{{ $this->getColspanCount() }}">
            <div class="flex justify-center items-center py-10 bg-white text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 9.75h.008v.008H9.75V9.75zm0 4.5h.008v.008H9.75V14.25zm4.5-4.5h.008v.008H14.25V9.75zm0 4.5h.008v.008H14.25V14.25zm.75 4.5H9a2.25 2.25 0 01-2.25-2.25v-9A2.25 2.25 0 019 5.25h6a2.25 2.25 0 012.25 2.25v9A2.25 2.25 0 0115 18.75z" />
                </svg>
                <span class="text-lg font-semibold text-gray-500">
                    {{ $this->getEmptyMessage() }}
                </span>
            </div>
        </td>
    </tr>
@elseif ($isBootstrap)
    <tr {{ $attributes }}>
        <td colspan="{{ $this->getColspanCount() }}" class="text-center text-muted py-4">
            {{ $this->getEmptyMessage() }}
        </td>
    </tr>
@endif
