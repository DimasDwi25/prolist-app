@extends('project-controller.layouts.app')

@section('content')
    <div>
        <form wire:submit.prevent="save">
            <div class="mb-4">
                <label class="block font-semibold">Schedule Name</label>
                <input type="text" wire:model="scheduleName" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-bold mb-2">Tasks</h3>

                @foreach ($tasks as $index => $task)
                    <div class="border rounded p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
                            <div>
                                <label class="block text-sm font-medium">Task</label>
                                <select wire:model="tasks.{{ $index }}.task_id" class="w-full border rounded p-2">
                                    <option value="">-- Choose Task --</option>
                                    @foreach($availableTasks as $availableTask)
                                        <option value="{{ $availableTask->id }}">{{ $availableTask->task_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Quantity</label>
                                <input type="number" wire:model="tasks.{{ $index }}.quantity" class="w-full border rounded p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Unit</label>
                                <input type="text" wire:model="tasks.{{ $index }}.unit" class="w-full border rounded p-2">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
                            <div>
                                <label class="block text-sm font-medium">Bobot (%)</label>
                                <input type="number" wire:model="tasks.{{ $index }}.bobot" step="0.01"
                                    class="w-full border rounded p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Plan Start</label>
                                <input type="date" wire:model="tasks.{{ $index }}.plan_start" class="w-full border rounded p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Plan Finish</label>
                                <input type="date" wire:model="tasks.{{ $index }}.plan_finish"
                                    class="w-full border rounded p-2">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
                            <div>
                                <label class="block text-sm font-medium">Actual Start</label>
                                <input type="date" wire:model="tasks.{{ $index }}.actual_start"
                                    class="w-full border rounded p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Actual Finish</label>
                                <input type="date" wire:model="tasks.{{ $index }}.actual_finish"
                                    class="w-full border rounded p-2">
                            </div>
                        </div>

                        <div class="mb-2">
                            <h4 class="text-sm font-bold mb-1">Weeks</h4>
                            @foreach($task['weeks'] as $weekIndex => $week)
                                <div class="flex items-center gap-2 mb-2">
                                    <input type="number" wire:model="tasks.{{ $index }}.weeks.{{ $weekIndex }}.week_number"
                                        class="w-20 border rounded p-1 text-sm" placeholder="Week">
                                    <input type="date" wire:model="tasks.{{ $index }}.weeks.{{ $weekIndex }}.week_start"
                                        class="w-36 border rounded p-1 text-sm">
                                    <input type="date" wire:model="tasks.{{ $index }}.weeks.{{ $weekIndex }}.week_end"
                                        class="w-36 border rounded p-1 text-sm">
                                    <input type="number" wire:model="tasks.{{ $index }}.weeks.{{ $weekIndex }}.bobot_plan"
                                        placeholder="Plan" step="0.01" class="w-24 border rounded p-1 text-sm">
                                    <input type="number" wire:model="tasks.{{ $index }}.weeks.{{ $weekIndex }}.bobot_actual"
                                        placeholder="Actual" step="0.01" class="w-24 border rounded p-1 text-sm">
                                    <button type="button" wire:click="removeWeek({{ $index }}, {{ $weekIndex }})"
                                        class="text-red-600 text-sm">✕</button>
                                </div>
                            @endforeach

                            <button type="button" wire:click="addWeek({{ $index }})"
                                class="text-sm text-blue-600 hover:underline">➕ Add Week</button>
                        </div>

                        <button type="button" wire:click="removeTask({{ $index }})" class="text-red-600 text-sm">🗑️ Remove
                            Task</button>
                    </div>
                @endforeach

                <button type="button" wire:click="addTask" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">➕ Add
                    Task</button>
            </div>

            <div class="text-right">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded text-sm">💾 Save Schedule</button>
            </div>
        </form>
    </div>
@endsection