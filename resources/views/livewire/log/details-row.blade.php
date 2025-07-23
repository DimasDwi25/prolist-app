<tr>
    <td colspan="6" class="bg-gray-50 px-4 py-3 text-sm text-gray-700">
        @if ($this->expandedRow === $row->id)
            <div class="whitespace-pre-line break-words">
                {{ $row->logs }}
            </div>
            <button class="mt-2 text-blue-600 text-xs hover:underline"
                wire:click="toggleRow({{ $row->id }})">
                Tutup
            </button>
        @endif
    </td>
</tr>
