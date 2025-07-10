@extends(Auth::user()->role->name === 'super_admin' ? 'admin.layouts.app' : 'marketing.layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Client List</h1>
        <a href="{{ route('client.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm shadow">
            + Add Client
        </a>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full table-auto border border-gray-200">
            <thead class="bg-gray-100 text-gray-700 text-sm">
                <tr>
                    <th class="px-4 py-2 text-left">#</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Phone</th>
                    <th class="px-4 py-2 text-left">City</th>
                    <th class="px-4 py-2 text-left">Country</th>
                    <th class="px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-800">
                @forelse ($clients as $index => $client)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ $client->name }}</td>
                        <td class="px-4 py-2">{{ $client->phone }}</td>
                        <td class="px-4 py-2">{{ $client->city }}</td>
                        <td class="px-4 py-2">{{ $client->country }}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('client.edit', $client) }}" class="text-blue-600 hover:underline text-sm mr-3">
                                ✏️ Edit
                            </a>

                            @if(Auth::user()->role->name === 'super_admin')
                                <form action="{{ route('client.destroy', $client) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to delete this client?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline text-sm">
                                        🗑 Delete
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center px-4 py-6 text-gray-500">
                            No clients found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection