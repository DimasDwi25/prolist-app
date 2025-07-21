@extends('project-controller.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-white rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">📄 Work Orders</h1>
        <a href="{{ route('project_controller.work-orders.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">➕ Create WO</a>
    </div>

    <table class="w-full table-auto border-collapse">
        <thead class="bg-gray-100">
            <tr class="text-left">
                <th class="p-2">WO Code</th>
                <th class="p-2">Project</th>
            
                <th class="p-2">WO Date</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($workOrders as $wo)
                <tr class="border-b">
                    <td class="p-2">{{ $wo->wo_kode_no }}</td>
                    <td class="p-2">{{ $wo->project->project_name }}</td>
                 
                    <td class="p-2">{{ \Carbon\Carbon::parse($wo->wo_date)->format('d M Y') }}</td>
                    <td class="p-2 space-x-2">
                        <a href="#" class="text-blue-600 hover:underline">View</a>
                        <a href="#" class="text-yellow-600 hover:underline">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">No work orders available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
