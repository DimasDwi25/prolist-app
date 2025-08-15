@php
    $userRole = auth()->user()->role->name ?? null;
@endphp

<div class="bg-white shadow rounded-lg p-4 w-full mx-auto">
    <h2 class="text-xl font-semibold mb-4">PHC Pending Validation</h2>

    @if($approvals->isEmpty())
        <p class="text-gray-500">Tidak ada PHC yang perlu divalidasi.</p>
    @else
        <table class="min-w-full border text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left">Project</th>
                    <th class="px-3 py-2 text-left">PHC</th>
                    <th class="px-3 py-2 text-left">Status</th>
                    <th class="px-3 py-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($approvals as $approval)
                    @php
                        $role = auth()->user()->role->name ?? null;
                        $phcId = $approval->phc_id;

                        switch ($role) {
                            case 'supervisor marketing':
                                $route = route('phc.show', $phcId); // karena semua role boleh akses ini
                                break;
                            case 'project controller':
                                $route = route('project_controller.phc.show', $phcId);
                                break;
                            case 'project manager':
                                $route = route('project_manager.phc.show', $phcId);
                                break;
                            default:
                                $route = route('phc.show', $phcId); // fallback universal show route
                                break;
                        }
                    @endphp

                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $approval->phc->project->project_number ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $approval->phc->title ?? 'PHC' }}</td>
                        <td class="px-3 py-2 text-yellow-600">{{ ucfirst($approval->status) }}</td>
                        <td class="px-3 py-2 text-center space-x-2">
                            <!-- Tombol View PHC -->
                            <a href="{{ $route }}"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded shadow text-xs">
                                View PHC
                            </a>

                            <!-- Tombol Validasi -->
                            <button wire:click="openValidationModal({{ $approval->id }})"
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded shadow text-xs">
                                Validasi
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
