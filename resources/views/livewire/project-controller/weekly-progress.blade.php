<div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
    {{-- Tombol Back --}}
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('projects.schedule-tasks.index', [$project->id, $schedule->id]) }}"
            class="inline-flex items-center text-sm text-gray-700 hover:text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" class="w-4 h-4 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>
        <h2 class="text-xl font-bold text-gray-800">
            Weekly Progress - {{ $task->task->task_name ?? 'Task' }}
        </h2>
    </div>

    <table class="border border-gray-400 text-xs text-gray-700 min-w-full">
        <thead>
            {{-- Header Bulan --}}
            <tr class="bg-gray-200 text-center font-bold">
                {{-- Hapus kolom ITEM --}}
                <th class="border p-2 min-w-[180px]">DESCRIPTION</th>
                <th class="border p-2">QTY</th>
                <th class="border p-2">UNIT</th>
                <th class="border p-2">BOBOT</th>

                @php
                    $months = [];
                    foreach ($weeks as $week) {
                        $month = \Carbon\Carbon::parse($week['week_start'])->format('F');
                        $months[$month][] = $week;
                    }
                @endphp

                @foreach ($months as $month => $weeksInMonth)
                    <th class="border p-2 text-center" colspan="{{ count($weeksInMonth) }}">
                        {{ strtoupper($month) }}
                    </th>
                @endforeach
            </tr>

            {{-- Header Week Number + Date --}}
            <tr class="bg-gray-100 text-center">
                {{-- Karena kolom ITEM dihapus, colspan jadi 4 --}}
                <th colspan="4" class="border p-2 text-left">WEEK</th>
                @foreach ($months as $weeksInMonth)
                    @foreach ($weeksInMonth as $week)
                        <th class="border p-2 min-w-[80px] relative group">
                            W{{ $week['week_number'] }}
                            <div class="text-[10px] text-gray-500">
                                {{ \Carbon\Carbon::parse($week['week_start'])->format('d M') }} -
                                {{ \Carbon\Carbon::parse($week['week_end'])->format('d M') }}
                            </div>

                            {{-- Tombol Delete --}}
                            <button type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 text-xs shadow
                                           opacity-0 group-hover:opacity-100 hover:scale-110 transform transition"
                                onclick="if(confirm('Delete this week?')) @this.call('deleteWeek', {{ $week['id'] }})"
                                title="Delete this week">
                                ✕
                            </button>
                        </th>
                    @endforeach
                @endforeach
            </tr>
        </thead>

        <tbody>
            {{-- Baris Plan --}}
            <tr>
                {{-- Kolom ITEM dihapus, jadi DESCRIPTION pindah ke sini --}}
                <td class="border p-2 text-left" rowspan="2">{{ $task->task->task_name ?? '-' }}</td>
                <td class="border p-2 text-center" rowspan="2">{{ $task->quantity }}</td>
                <td class="border p-2 text-center" rowspan="2">{{ $task->unit ?? '-' }}</td>
                <td class="border p-2 text-center" rowspan="2">{{ number_format($task->bobot, 2) }}%</td>

                @foreach ($weeks as $week)
                    <td class="border p-2 text-center">
                        <span class="block text-[10px] italic text-gray-600">Plan</span>
                        <div class="relative w-16 mx-auto">
                            <input type="number" min="0" step="0.01"
                                class="w-full border rounded p-1 text-xs text-center pr-5" value="{{ $week['bobot_plan'] }}"
                                wire:change="updateBobot({{ $week['id'] }}, 'bobot_plan', $event.target.value)">
                            <span class="absolute right-1 top-1 text-xs text-gray-500">%</span>
                        </div>
                    </td>
                @endforeach
            </tr>

            {{-- Baris Actual --}}
            <tr>
                @foreach ($weeks as $week)
                    <td class="border p-2 text-center">
                        <span class="block text-[10px] italic text-gray-600">Actual</span>
                        <div class="relative w-16 mx-auto">
                            <input type="number" min="0" step="0.01"
                                class="w-full border rounded p-1 text-xs text-center pr-5"
                                value="{{ $week['bobot_actual'] }}"
                                wire:change="updateBobot({{ $week['id'] }}, 'bobot_actual', $event.target.value)">
                            <span class="absolute right-1 top-1 text-xs text-gray-500">%</span>
                        </div>
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>

</div>