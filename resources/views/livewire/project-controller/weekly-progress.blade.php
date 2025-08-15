<div class="bg-white rounded-xl shadow p-6 relative">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('projects.schedule-tasks.index', [$project->pn_number, $schedule->id]) }}"
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

    {{-- Scroll container --}}
    <div class="relative">
        {{-- Scrollable area with proper width constraints --}}
        <div class="relative overflow-x-auto pb-2 max-w-screen-xl">
            <div class="inline-block min-w-max">
                <table class="border border-gray-400 text-xs text-gray-700">
                    <thead>
                        <tr class="bg-gray-200 text-center font-bold">
                            <th class="border p-2 min-w-[180px] sticky left-0 bg-gray-200 z-20">DESCRIPTION</th>
                            <th class="border p-2 min-w-[50px] sticky left-[180px] bg-gray-200 z-20">QTY</th>
                            <th class="border p-2 min-w-[50px] sticky left-[230px] bg-gray-200 z-20">UNIT</th>
                            <th class="border p-2 min-w-[50px] sticky left-[280px] bg-gray-200 z-20">BOBOT</th>

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

                        <tr class="bg-gray-100 text-center">
                            <th colspan="4" class="border p-2 text-left sticky left-0 bg-gray-100 z-10">WEEK</th>
                            @foreach ($months as $weeksInMonth)
                                @foreach ($weeksInMonth as $week)
                                    <th class="border p-2 min-w-[80px] relative group whitespace-nowrap">
                                        W{{ $week['week_number'] }}
                                        <div class="text-[10px] text-gray-500 whitespace-normal">
                                            {{ \Carbon\Carbon::parse($week['week_start'])->format('d M') }} -
                                            {{ \Carbon\Carbon::parse($week['week_end'])->format('d M') }}
                                        </div>
                                        <button type="button"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 text-xs shadow opacity-0 group-hover:opacity-100 hover:scale-110 transform transition"
                                            onclick="if(confirm('Delete this week?')) @this.call('deleteWeek', {{ $week['id'] }})"
                                            title="Delete this week">✕</button>
                                    </th>
                                @endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border p-2 text-left sticky left-0 bg-white z-10 whitespace-nowrap" rowspan="2">
                                {{ $task->task->task_name ?? '-' }}
                            </td>
                            <td class="border p-2 text-center sticky left-[180px] bg-white z-10" rowspan="2">
                                {{ $task->quantity }}
                            </td>
                            <td class="border p-2 text-center sticky left-[230px] bg-white z-10" rowspan="2">
                                {{ $task->unit ?? '-' }}
                            </td>
                            <td class="border p-2 text-center sticky left-[280px] bg-white z-10" rowspan="2">
                                {{ number_format($task->bobot, 2) }}%
                            </td>

                            @foreach ($weeks as $week)
                                <td class="border p-2 text-center">
                                    <span class="block text-[10px] italic text-gray-600">Plan</span>
                                    <div class="relative w-16 mx-auto">
                                        <input type="number" min="0" step="0.01"
                                            class="w-full border rounded p-1 text-xs text-center pr-5"
                                            value="{{ $week['bobot_plan'] }}"
                                            wire:change="updateBobot({{ $week['id'] }}, 'bobot_plan', $event.target.value)">
                                        <span class="absolute right-1 top-1 text-xs text-gray-500">%</span>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
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
        </div>
    </div>
</div>