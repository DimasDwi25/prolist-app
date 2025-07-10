@extends('marketing.layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Quotations</h1>
        <a href="{{ route('quotation.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">+ New</a>
    </div>

    @if (session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        @if ($quotations->isEmpty())
            <div class="p-6 text-gray-600 text-center">No quotations available.</div>
        @else
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">No</th>
                        <th class="px-4 py-2 text-left">Client</th>
                        <th class="px-4 py-2 text-left">Title</th>
                        <th class="px-4 py-2 text-left">PIC</th>
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">Value</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($quotations as $quotation)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $quotation->no_quotation }}</td>
                            <td class="px-4 py-2">{{ $quotation->client->name }}</td>
                            <td class="px-4 py-2">{{ $quotation->title_quotation }}</td>
                            <td class="px-4 py-2">{{ $quotation->client_pic }}</td>
                            <td class="px-4 py-2">
                                {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('d M Y') }}
                            </td>

                            <td class="px-4 py-2">Rp{{ number_format($quotation->quotation_value, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 space-x-2">
                                {{-- View --}}
                                <a href="{{ route('quotation.show', $quotation) }}" class="text-green-600 hover:underline">View</a>

                                {{-- Edit --}}
                                <a href="{{ route('quotation.edit', $quotation) }}" class="text-blue-600 hover:underline">Edit</a>

                                {{-- Change Status --}}
                                <!-- Status Badge + Trigger Modal -->
                                <div x-data="{ open: false }" class="inline-block">
                                    @php
                                        $statusMap = [
                                            'A' => ['label' => '✓ Completed', 'color' => 'green'],
                                            'D' => ['label' => '⏳ Belum ada PO', 'color' => 'yellow'],
                                            'E' => ['label' => '❌ Dibatalkan', 'color' => 'gray'],
                                            'F' => ['label' => '⚠️ Kalah Tender', 'color' => 'red'],
                                            'O' => ['label' => '🕒 On Going', 'color' => 'blue'],
                                        ];
                                        $statusInfo = $statusMap[$quotation->status] ?? ['label' => 'Unknown', 'color' => 'gray'];
                                    @endphp

                                    <button @click="open = true"
                                        class="px-3 py-1 text-xs rounded bg-{{ $statusInfo['color'] }}-100 text-{{ $statusInfo['color'] }}-800">
                                        {{ $statusInfo['label'] }}
                                    </button>

                                    <!-- Modal -->
                                    <div x-show="open" @click.away="open = false"
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
                                        <div class="bg-white rounded-lg p-6 w-80 shadow">
                                            <h3 class="text-lg font-semibold mb-4">Ubah Status Quotation</h3>
                                            <form method="POST" action="{{ route('quotation.updateStatus', $quotation) }}">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="w-full mb-4 border-gray-300 rounded">
                                                    @foreach ($statusMap as $key => $info)
                                                        <option value="{{ $key }}" @selected($quotation->status === $key)>
                                                            {{ $info['label'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="flex justify-between items-center">
                                                    <button type="button" @click="open = false"
                                                        class="text-sm text-gray-500 hover:underline">Cancel</button>
                                                    <button type="submit"
                                                        class="bg-blue-600 text-white px-4 py-2 text-sm rounded hover:bg-blue-700">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Delete --}}
                                @if(Auth::user()?->role?->name === 'super_admin')
                                    <form action="{{ route('quotation.destroy', $quotation) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Delete this quotation?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 hover:underline">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection