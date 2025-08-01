<div class="bg-white rounded-xl shadow p-6 relative">
    {{-- Header with project info --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">
                Weekly Progress - {{ $project->project_number }}
            </h2>
            <p class="text-sm text-gray-600">{{ $project->name }}</p>
        </div>
        <div class="flex space-x-2">
            <button wire:click="$set('showScheduleModal', true)"
                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 text-sm rounded">
                + Schedule
            </button>
            <button wire:click="$set('showTaskModal', true)"
                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 text-sm rounded">
                + Task
            </button>
        </div>
    </div>

    {{-- Scroll container with improved UX --}}
    <div class="relative">
        <div class="relative overflow-x-auto pb-2 max-w-screen-xl">
            <div class="inline-block min-w-max">
                <table class="border border-gray-300 text-xs text-gray-700">
                    <thead>
                        <tr class="bg-gray-200 text-center font-bold">
                            <th class="border p-2 min-w-[180px] sticky left-0 bg-gray-200 z-30">DESCRIPTION</th>
                            <th class="border p-2 min-w-[50px] sticky left-[180px] bg-gray-200 z-30">QTY</th>
                            <th class="border p-2 min-w-[50px] sticky left-[230px] bg-gray-200 z-30">UNIT</th>
                            <th class="border p-2 min-w-[50px] sticky left-[280px] bg-gray-200 z-30">BOBOT</th>

                            @php
                                // Group weeks by month for header display
                                $months = [];
                                $currentYear = date('Y');
                                foreach ($weeks as $week) {
                                    $month = \Carbon\Carbon::parse($week['week_start'])->format('F');
                                    $year = \Carbon\Carbon::parse($week['week_start'])->format('Y');
                                    $monthKey = $month . ($year != $currentYear ? ' ' . $year : '');
                                    $months[$monthKey][] = $week;
                                }
                            @endphp

                            @foreach ($months as $month => $weeksInMonth)
                                <th class="border p-2 text-center bg-gray-200" colspan="{{ count($weeksInMonth) }}">
                                    {{ strtoupper($month) }}
                                </th>
                            @endforeach
                        </tr>

                        <tr class="bg-gray-100 text-center">
                            <th colspan="4" class="border p-2 text-left sticky left-0 bg-gray-100 z-20">WEEK</th>
                            @foreach ($months as $weeksInMonth)
                                @foreach ($weeksInMonth as $week)
                                    <th class="border p-2 min-w-[80px] relative group whitespace-nowrap bg-gray-100">
                                        <div class="flex flex-col items-center">
                                            <span class="font-medium">W{{ $week['week_number'] }}</span>
                                            <span class="text-[10px] text-gray-500">
                                                {{ \Carbon\Carbon::parse($week['week_start'])->format('d M') }} -
                                                {{ \Carbon\Carbon::parse($week['week_end'])->format('d M') }}
                                            </span>
                                        </div>
                                    </th>
                                @endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            {{-- Parent Row --}}
                            <tr class="bg-gray-100 font-semibold hover:bg-gray-150 transition-colors">
                                <td class="border p-2 sticky left-0 bg-gray-100 z-20 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="ml-1">{{ $schedule['name'] }}</span>
                                    </div>
                                </td>
                                <td class="border p-2 text-center sticky left-[180px] bg-gray-100 z-20"></td>
                                <td class="border p-2 text-center sticky left-[230px] bg-gray-100 z-20"></td>
                                <td class="border p-2 text-center sticky left-[280px] bg-gray-100 z-20"></td>

                                @foreach ($weeks as $week)
                                    @php
                                        $weekTotalPlan = collect($schedule['tasks'])->sum(fn($t) =>
                                            collect($t['week_schedules'])->firstWhere('week_number', $week['week_number'])['bobot_plan'] ?? 0);
                                    @endphp
                                    <td class="border p-2 text-center bg-gray-100">
                                        <span class="font-medium"></span>
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Task Rows --}}
                            @foreach ($schedule['tasks'] as $task)
                                {{-- Plan Row --}}
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="border p-2 pl-6 sticky left-0 bg-white z-10 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="w-1 h-4 bg-blue-500 mr-2 rounded-full"></span>
                                            {{ $task['task']['task_name'] ?? '(no name)' }}
                                        </div>
                                    </td>
                                    <td class="border p-2 text-center sticky left-[180px] bg-white z-10">
                                        {{ $task['quantity'] ?? '-' }}
                                    </td>
                                    <td class="border p-2 text-center sticky left-[230px] bg-white z-10">
                                        {{ $task['unit'] ?? '-' }}
                                    </td>
                                    <td class="border p-2 text-center sticky left-[280px] bg-white z-10">
                                        <span class="font-medium">
                                            {{ isset($task['bobot']) ? number_format($task['bobot'], 2) . '%' : '-' }}
                                        </span>
                                    </td>

                                    @foreach ($weeks as $week)
                                        @php
                                            $weekData = collect($task['week_schedules'])->firstWhere('week_number', $week['week_number']);
                                            $plan = $weekData['bobot_plan'] ?? 0;
                                            $weekId = $weekData['id'] ?? null;
                                            $isLastWeek = $loop->last;
                                        @endphp
                                        <td class="border p-2 text-center @if($isLastWeek) bg-yellow-50 @endif">
                                            <div class="flex flex-col items-center space-y-1">
                                                <span class="text-[10px] italic text-gray-600">Plan</span>
                                                <div class="relative">
                                                    <!-- Plan Row Input -->
                                                    <input type="number" min="0" step="0.01" max="100"
                                                        class="w-16 border rounded p-1 text-xs text-center pr-5 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 transition
                                                            @if($isLastWeek) border-yellow-300 bg-yellow-50 @endif
                                                            @if($weekData['disabled'] ?? false) bg-gray-100 cursor-not-allowed @endif" value="{{ $plan }}"
                                                        wire:change.debounce.500ms="updateBobot({{ $weekId }}, 'bobot_plan', $event.target.value, {{ $task['id'] }})"
                                                        @if($weekData['disabled'] ?? false) disabled @endif x-data="{
                                                        previousValue: {{ $plan }},
                                                        handleChange(event) {
                                                            const weekNumber = {{ $weekData['week_number'] ?? 0 }};
                                                            const newValue = parseFloat(event.target.value);

                                                            // Check if previous week was 100%
                                                            if (weekNumber > 1) {
                                                                const prevWeekInput = this.$root.querySelector(`input[data-week-number='${weekNumber-1}'][data-task-id='{{ $task['id'] }}'][data-type='plan']`);
                                                                if (prevWeekInput && parseFloat(prevWeekInput.value) === 100) {
                                                                    event.target.value = 100;
                                                                    this.previousValue = 100;
                                                                    $wire.updateBobot({{ $weekId }}, 'bobot_plan', 100, {{ $task['id'] }});
                                                                    return;
                                                                }
                                                            }

                                                            this.previousValue = newValue;
                                                            $wire.updateBobot({{ $weekId }}, 'bobot_plan', newValue, {{ $task['id'] }});
                                                        }
                                                    }" x-on:change="handleChange(event)"
                                                        data-week-number="{{ $weekData['week_number'] ?? 0 }}"
                                                        data-task-id="{{ $task['id'] }}" data-type="plan">
                                                    <span class="absolute right-1 top-1 text-xs text-gray-500">%</span>
                                                </div>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>

                                {{-- Actual Row --}}
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="border p-2 pl-6 sticky left-0 bg-blue-50 z-10 whitespace-nowrap">
                                        <div class="flex items-center pl-3">
                                            <span class="text-[10px] italic text-gray-500">Actual Progress</span>
                                        </div>
                                    </td>
                                    <td class="border p-2 sticky left-[180px] bg-blue-50 z-10"></td>
                                    <td class="border p-2 sticky left-[230px] bg-blue-50 z-10"></td>
                                    <td class="border p-2 sticky left-[280px] bg-blue-50 z-10"></td>

                                    @foreach ($weeks as $week)
                                                                @php
                                                                    $weekData = collect($task['week_schedules'])->firstWhere('week_number', $week['week_number']);
                                                                    $actual = $weekData['bobot_actual'] ?? 0;
                                                                    $weekId = $weekData['id'] ?? null;
                                                                    $isLastWeek = $loop->last;
                                                                @endphp
                                                                <td class="border p-2 text-center bg-blue-50 @if($isLastWeek) bg-yellow-50 @endif">
                                                                    <div class="flex flex-col items-center space-y-1">
                                                                        <span class="text-[10px] italic text-gray-600">Actual</span>
                                                                        <div class="relative">
                                                                            <!-- Actual Row Input -->
                                                                            <input type="number" min="0" step="0.01" max="100" class="w-16 border rounded p-1 text-xs text-center pr-5 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 transition
                                                @if($isLastWeek) border-yellow-300 bg-yellow-50 @endif
                                                @if($weekData['disabled'] ?? false) bg-gray-100 cursor-not-allowed @endif" value="{{ $actual }}"
                                                                                wire:change.debounce.500ms="updateBobot({{ $weekId }}, 'bobot_actual', $event.target.value, {{ $task['id'] }})"
                                                                                @if($weekData['disabled'] ?? false) disabled @endif x-data="{
                                            previousValue: {{ $actual }},
                                            handleChange(event) {
                                                const weekNumber = {{ $weekData['week_number'] ?? 0 }};
                                                const newValue = parseFloat(event.target.value);

                                                // Check if previous week was 100%
                                                if (weekNumber > 1) {
                                                    const prevWeekInput = this.$root.querySelector(`input[data-week-number='${weekNumber-1}'][data-task-id='{{ $task['id'] }}'][data-type='actual']`);
                                                    if (prevWeekInput && parseFloat(prevWeekInput.value) === 100) {
                                                        event.target.value = 100;
                                                        this.previousValue = 100;
                                                        $wire.updateBobot({{ $weekId }}, 'bobot_actual', 100, {{ $task['id'] }});
                                                        return;
                                                    }
                                                }

                                                this.previousValue = newValue;
                                                $wire.updateBobot({{ $weekId }}, 'bobot_actual', newValue, {{ $task['id'] }});
                                            }
                                        }" x-on:change="handleChange(event)" data-week-number="{{ $weekData['week_number'] ?? 0 }}"
                                                                                data-task-id="{{ $task['id'] }}" data-type="actual">
                                                                            <span class="absolute right-1 top-1 text-xs text-gray-500">%</span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Schedule Modal -->
    <div x-data="{ open: @entangle('showScheduleModal') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="$wire.set('showScheduleModal', false)" type="button"
                        class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Add New Schedule</h3>
                    <div class="mt-2">
                        <div class="mb-4">
                            <label for="scheduleName" class="block text-sm font-medium text-gray-700">Schedule
                                Name</label>
                            <input wire:model.defer="newSchedule.name" type="text" id="scheduleName"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('newSchedule.name') <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button wire:click="saveSchedule" type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                        Save
                    </button>
                    <button @click="$wire.set('showScheduleModal', false)" type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div x-data="{ open: @entangle('showTaskModal') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="$wire.set('showTaskModal', false)" type="button"
                        class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Add New Task</h3>
                    @if ($project->phc && $project->phc->start_date)
                        <div class="mb-4 text-sm text-gray-700">
                            <strong>PHC Start Date:</strong>
                            {{ \Carbon\Carbon::parse($project->phc->start_date)->format('d M Y') }}
                        </div>
                    @endif

                    <div class="mt-2">
                        <div class="mb-4">
                            <label for="taskSchedule" class="block text-sm font-medium text-gray-700">Schedule</label>
                            <select wire:model.defer="newTask.schedule_id" id="taskSchedule"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select Schedule</option>
                                @foreach($schedules as $schedule)
                                    <option value="{{ $schedule['id'] }}">{{ $schedule['name'] }}</option>
                                @endforeach
                            </select>
                            @error('newTask.schedule_id') <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="taskName" class="block text-sm font-medium text-gray-700">Task</label>
                            <select wire:model.defer="newTask.task_id" id="taskName"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select Task</option>
                                @foreach($tasks as $task)
                                    <option value="{{ $task->id }}">{{ $task->task_name }}</option>
                                @endforeach
                            </select>
                            @error('newTask.task_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                                <input wire:model.defer="newTask.quantity" type="number" id="quantity"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('newTask.quantity') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
                                <input wire:model.defer="newTask.unit" type="text" id="unit"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('newTask.unit') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="bobot" class="block text-sm font-medium text-gray-700">Bobot (%)</label>
                            <input wire:model.defer="newTask.bobot" type="number" min="0" max="100" step="0.01"
                                id="bobot"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('newTask.bobot') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="planStart" class="block text-sm font-medium text-gray-700">Plan
                                    Start</label>
                                <input wire:model.defer="newTask.plan_start" type="date" id="planStart"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('newTask.plan_start') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="planFinish" class="block text-sm font-medium text-gray-700">Plan
                                    Finish</label>
                                <input wire:model.defer="newTask.plan_finish" type="date" id="planFinish"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('newTask.plan_finish') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="actualStart" class="block text-sm font-medium text-gray-700">Actual
                                    Start</label>
                                <input wire:model.defer="newTask.actual_start" type="date" id="actualStart"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('newTask.actual_start') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="actualFinish" class="block text-sm font-medium text-gray-700">Actual
                                    Finish</label>
                                <input wire:model.defer="newTask.actual_finish" type="date" id="actualFinish"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('newTask.actual_finish') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button wire:click="saveTask" type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Save
                    </button>
                    <button @click="$wire.closeTaskModal()" type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>