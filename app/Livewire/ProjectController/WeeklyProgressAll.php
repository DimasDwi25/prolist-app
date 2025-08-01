<?php

namespace App\Livewire\ProjectController;

use App\Models\Project;
use App\Models\ProjectSchedule;
use App\Models\ProjectScheduleTask;
use App\Models\ProjectWeekSchedule;
use App\Models\Task;
use Carbon\Carbon;
use Livewire\Component;

class WeeklyProgressAll extends Component
{
    public Project $project;
    public array $schedules = [];
    public array $weeks = [];
    public $tasks;

    public $showScheduleModal = false;
    public $showTaskModal = false;


    public $newSchedule = [
        'name' => '',
    ];

    public $newTask = [
        'schedule_id' => null,
        'task_id' => null,
        'quantity' => 1,
        'unit' => '',
        'bobot' => 0,
        'plan_start' => '',
        'plan_finish' => '',
        'actual_start' => '',
        'actual_finish' => '',
    ];

    public function mount(Project $project)
    {
        $this->project = $project;

        $this->schedules = ProjectSchedule::with('tasks')->where('project_id', $project->id)->get()->toArray();
        $this->generateWeeks();

        $this->loadData();
    }

    public function generateWeeks()
    {
        $this->weeks = [];

        foreach ($this->project->schedules as $schedule) {
            $phcStartDate = optional($this->project->phc)->start_date;
            $planStartDate = $schedule->plan_start;

            if (!$phcStartDate || !$planStartDate) {
                continue;
            }

            $start = Carbon::parse($phcStartDate)->startOfWeek(Carbon::MONDAY);
            $end = Carbon::parse($planStartDate)->startOfWeek(Carbon::MONDAY);
            $weeks = [];

            $i = 1;
            while ($start <= $end) {
                $weeks[] = [
                    'week' => $i,
                    'start' => $start->copy()->format('Y-m-d'),
                    'end' => $start->copy()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d'),
                ];
                $start->addWeek();
                $i++;
            }

            $this->weeks[$schedule->id] = $weeks;
        }
    }

    public function loadData()
    {
        $this->tasks = Task::all();

        $this->schedules = ProjectSchedule::with([
            'tasks.task',
            'tasks.weekSchedules'
        ])
            ->where('project_id', $this->project->id)
            ->get(['id', 'project_id', 'name'])
            ->toArray();

        $weekData = ProjectWeekSchedule::whereIn(
            'project_schedule_task_id',
            collect($this->schedules)->flatMap(fn($s) => collect($s['tasks'])->pluck('id'))->toArray()
        )
            ->orderBy('week_number')
            ->get()
            ->map(function ($week) {
                return [
                    'week_number' => $week->week_number,
                    'week_start' => $week->week_start,
                    'week_end' => Carbon::parse($week->week_start)->addDays(6)->toDateString(),
                ];
            })
            ->unique('week_number')
            ->values();

        $this->weeks = $weekData->toArray();

        // Add disabled weeks information for each task
        foreach ($this->schedules as &$schedule) {
            foreach ($schedule['tasks'] as &$task) {
                $taskPlanStart = Carbon::parse($task['plan_start']);
                $phcStart = Carbon::parse($this->project->phc->start_date ?? now());

                // Calculate the week number where plan starts
                $planStartWeekNumber = $phcStart->diffInWeeks($taskPlanStart) + 1;

                // Mark weeks before plan start as disabled
                foreach ($task['week_schedules'] as &$weekSchedule) {
                    $weekSchedule['disabled'] = $weekSchedule['week_number'] < $planStartWeekNumber;
                    if ($weekSchedule['disabled']) {
                        $weekSchedule['bobot_plan'] = 0;
                        $weekSchedule['bobot_actual'] = 0;
                    }
                }
            }
        }
    }

    public function saveSchedule()
    {
        $this->validate([
            'newSchedule.name' => 'required|string|max:255',
        ]);

        ProjectSchedule::create([
            'project_id' => $this->project->id,
            'name' => $this->newSchedule['name'],
        ]);

        $this->closeScheduleModal();
        $this->loadData();
    }

    public function saveTask()
    {
        $this->validate([
            'newTask.schedule_id' => 'required|exists:project_schedules,id',
            'newTask.task_id' => 'required|exists:tasks,id',
            'newTask.quantity' => 'required|numeric|min:1',
            'newTask.unit' => 'required|string|max:50',
            'newTask.bobot' => 'required|numeric|min:0|max:100',
            'newTask.plan_start' => 'required|date',
            'newTask.plan_finish' => 'required|date|after_or_equal:newTask.plan_start',
            'newTask.actual_start' => 'nullable|date',
            'newTask.actual_finish' => 'nullable|date|after_or_equal:newTask.actual_start'
        ]);

        $task = ProjectScheduleTask::create([
            'project_schedule_id' => $this->newTask['schedule_id'],
            'task_id' => $this->newTask['task_id'],
            'quantity' => $this->newTask['quantity'],
            'unit' => $this->newTask['unit'],
            'bobot' => $this->newTask['bobot'],
            'plan_start' => $this->newTask['plan_start'],
            'plan_finish' => $this->newTask['plan_finish'],
            'actual_start' => $this->newTask['actual_start'] ?: null,
            'actual_finish' => $this->newTask['actual_finish'] ?: null,
        ]);

        // Generate minggu dari PHC start_date ke plan_start
        $phcStart = optional($this->project->phc)->start_date;
        $planStart = Carbon::parse($this->newTask['plan_start']);

        if ($phcStart) {
            $start = Carbon::parse($phcStart)->startOfWeek(Carbon::MONDAY);
            $end = $planStart->startOfWeek(Carbon::MONDAY);
            $weekNumber = 1;

            while ($start <= $end) {
                ProjectWeekSchedule::create([
                    'project_schedule_task_id' => $task->id,
                    'week_number' => $weekNumber,
                    'week_start' => $start->copy()->format('Y-m-d'),
                    'bobot_plan' => 0,
                    'bobot_actual' => 0,
                ]);

                $start->addWeek();
                $weekNumber++;
            }
        }

        $this->closeTaskModal();
        $this->loadData();
    }

    public function closeScheduleModal()
    {
        $this->showScheduleModal = false;
        $this->reset('newSchedule');
    }

    public function closeTaskModal()
    {
        $this->showTaskModal = false;
        $this->reset('newTask');
    }

    public function updateBobot($weekId, $field, $value, $taskId)
    {
        if (!in_array($field, ['bobot_plan', 'bobot_actual'])) {
            return;
        }

        $week = ProjectWeekSchedule::find($weekId);
        if ($week) {
            // Check if this week should be disabled
            $task = ProjectScheduleTask::find($taskId);
            $phcStart = Carbon::parse($this->project->phc->start_date ?? now());
            $taskPlanStart = Carbon::parse($task->plan_start);
            $planStartWeekNumber = $phcStart->diffInWeeks($taskPlanStart) + 1;

            if ($week->week_number < $planStartWeekNumber) {
                return; // Skip update for disabled weeks
            }

            // Check if previous week was 100%
            if ($week->week_number > 1) {
                $previousWeek = ProjectWeekSchedule::where('project_schedule_task_id', $week->project_schedule_task_id)
                    ->where('week_number', $week->week_number - 1)
                    ->first();

                if ($previousWeek && $previousWeek->{$field} == 100) {
                    $value = 100; // Automatically set to 100 if previous week was 100
                }
            }

            $week->{$field} = $value;
            $week->save();

            // If we set this week to 100, automatically set all future weeks to 100
            if ($value == 100) {
                ProjectWeekSchedule::where('project_schedule_task_id', $week->project_schedule_task_id)
                    ->where('week_number', '>', $week->week_number)
                    ->update([$field => 100]);
            }

            // Check if this is the last week and we should create a new one
            $lastWeekNumber = ProjectWeekSchedule::where('project_schedule_task_id', $week->project_schedule_task_id)
                ->max('week_number');

            if ($week->week_number == $lastWeekNumber) {
                $this->createNewWeek($week->project_schedule_task_id);
            }

            $this->loadData();
        }
    }

    protected function createNewWeek($taskId)
    {
        $lastWeek = ProjectWeekSchedule::where('project_schedule_task_id', $taskId)
            ->orderByDesc('week_number')
            ->first();

        if ($lastWeek) {
            ProjectWeekSchedule::create([
                'project_schedule_task_id' => $taskId,
                'week_number' => $lastWeek->week_number + 1,
                'week_start' => Carbon::parse($lastWeek->week_start)->addWeek(),
                'bobot_plan' => 0,
                'bobot_actual' => 0,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.project-controller.weekly-progress-all')
            ->layout('project-controller.layouts.app');
    }
}