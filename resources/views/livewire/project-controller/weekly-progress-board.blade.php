<div class="bg-white shadow rounded-xl p-6 overflow-x-auto">
    <h2 class="text-xl font-semibold mb-4">📊 Weekly Progress Board - {{ $project->project_number }}</h2>

    <table class="table-auto text-sm border-collapse border w-full">
        <thead>
            <tr class="bg-gray-100 text-center">
                <th class="border px-2 py-1">Task</th>
                <th class="border px-2 py-1">Qty</th>
                <th class="border px-2 py-1">Unit</th>
                <th class="border px-2 py-1">Bobot</th>
                @foreach ($weeks as $week)
                    <th class="border px-2 py-1">
                        W{{ $week['week_number'] }}<br>
                        <span class="text-[10px] text-gray-500">
                            {{ $week['start']->format('d M') }} - {{ $week['end']->format('d M') }}
                        </span>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
                <tr>
                    <td class="border px-2 py-1">{{ $task->task->task_name ?? '-' }}</td>
                    <td class="border px-2 py-1 text-center">{{ $task->quantity }}</td>
                    <td class="border px-2 py-1 text-center">{{ $task->unit }}</td>
                    <td class="border px-2 py-1 text-center">{{ number_format($task->bobot, 2) }}%</td>
                    @foreach ($weeks as $week)
                        @php
                            $weekData = $task->weeks->firstWhere('week_start', $week['start']->toDateString());
                        @endphp
                        <td class="border px-2 py-1">
                            <div class="text-[10px] text-gray-500 italic">Plan</div>
                            <input type="number" min="0" step="0.01" value="{{ $weekData->bobot_plan ?? 0 }}"
                                wire:change="updateBobot({{ $weekData->id ?? 'null' }}, 'bobot_plan', $event.target.value)"
                                class="w-16 text-xs text-center border rounded px-1">

                            <div class="text-[10px] text-gray-500 italic">Actual</div>
                            <input type="number" min="0" step="0.01" value="{{ $weekData->bobot_actual ?? 0 }}"
                                wire:change="updateBobot({{ $weekData->id ?? 'null' }}, 'bobot_actual', $event.target.value)"
                                class="w-16 text-xs text-center border rounded px-1">
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>