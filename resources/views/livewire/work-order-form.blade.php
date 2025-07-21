@extends('engineer.layouts.app')

@section('content')
<div>
    <form wire:submit="save" class="space-y-6 max-w-4xl mx-auto p-6">
        <h2 class="text-xl font-bold mb-4">Create Work Order</h2>

        <div class="mb-4">
            <label class="block mb-1">Project Number</label>
            <select wire:model.live="project_id" class="w-full border rounded p-2">
                <option value="">-- Select Project --</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->project_number }}</option>
                @endforeach
            </select>
            @if ($client_name)
                <p class="mt-2 text-sm text-gray-600">Client: {{ $client_name }}</p>
            @endif
        </div>

        <div class="mb-4">
            <label class="block mb-1">WO Date</label>
            <input type="date" wire:model.live="wo_date" class="w-full border rounded p-2">
        </div>

        <div class="mb-4">
            <label class="block mb-1">WO Kode No</label>
            <input type="text" wire:model.live="wo_kode_no" class="w-full border rounded p-2 bg-gray-100" readonly>
        </div>

        <input type="hidden" wire:model.live="wo_number_in_project">

        <div class="grid grid-cols-2 gap-4">
            @for ($i = 1; $i <= 5; $i++)
                <div>
                    <label class="block mb-1">PIC {{ $i }}</label>
                    <select wire:model.live="pic{{ $i }}" class="w-full border rounded p-2">
                        <option value="">-- Select User --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1">Role PIC {{ $i }}</label>
                    <select wire:model.live="role_pic_{{ $i }}" class="w-full border rounded p-2">
                        <option value="">-- Select Role --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endfor
        </div>

        <div class="mb-4">
            <label class="block mb-1">Total Mandays Engineering</label>
            <input type="number" wire:model.live="total_mandays_eng" class="w-full border rounded p-2">
        </div>

        <div class="mb-4">
            <label class="block mb-1">Total Mandays Electrical</label>
            <input type="number" wire:model.live="total_mandays_elect" class="w-full border rounded p-2">
        </div>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" wire:model.live="add_work" class="mr-2">
                Tambahan Pekerjaan (Add Work)
            </label>
        </div>

        <div class="mb-4">
            <label class="block mb-1">Work Description</label>
            <textarea wire:model.live="work_description" class="w-full border rounded p-2"></textarea>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Submit</button>
    </form>

    @if ($showLogModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow-lg max-w-md w-full">
                <h2 class="text-lg font-semibold mb-4">Tambahkan deskripsi pekerjaan ke log proyek?</h2>
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('showLogModal', false)" class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                    <button wire:click="confirmAddToLog" class="px-4 py-2 bg-green-600 text-white rounded">Ya, Tambahkan</button>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
