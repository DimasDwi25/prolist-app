<table class="min-w-full table-auto border">
    <thead class="bg-gray-100 text-gray-600 text-sm">
        <tr>
            <th class="px-4 py-2 border text-left">#</th>
            <th class="px-4 py-2 border text-left">Nama</th>
        
        </tr>
    </thead>
    <tbody>
        @forelse ($subClients as $index => $subClient)
            <tr class="text-sm text-gray-700">
                <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                <td class="px-4 py-2 border">{{ $subClient->name }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-4 text-gray-500">Belum ada sub client.</td>
            </tr>
        @endforelse
    </tbody>
</table>
