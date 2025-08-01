<?php

namespace App\Livewire\ProjectController;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Project;
use App\Models\ProjectScheduleTask;
use App\Models\ProjectWeekSchedule;

class WeeklyProgressBoard extends Component
{
    public Project $project;
    public $weeks = [];
    public $tasks = [];

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->loadTasksAndWeeks(); // <== HARUS dipanggil
    }

    public function generateWeeksFromPHC()
    {
        $phc = $this->project->phc_dates ?? now();
        $start = Carbon::parse($phc)->startOfWeek(Carbon::MONDAY);
        $end = now()->endOfWeek(Carbon::SUNDAY);

        $weekList = [];
        $weekNumber = 1;

        while ($start <= $end) {
            $weekList[] = [
                'week_number' => $weekNumber,
                'start' => $start->copy(),
                'end' => $start->copy()->endOfWeek(Carbon::SUNDAY),
            ];
            $start->addWeek();
            $weekNumber++;
        }

        $this->weeks = $weekList;
    }

    public function loadTasksAndWeeks()
    {
        $this->tasks = ProjectScheduleTask::with([
            'task',
            'weeks' => function ($q) {
                $q->orderBy('week_number');
            },
            'schedule' // penting: agar bisa akses `schedule->name` dll nanti
        ])
            ->whereHas('schedule', function ($query) {
                $query->where('project_id', $this->project->id);
            })
            ->get();
    }



    public function updateBobot($weekId, $field, $value)
    {
        $week = ProjectWeekSchedule::find($weekId);
        if ($week) {
            $week->update([$field => is_numeric($value) ? $value : 0]);
        }

        $this->loadTasksAndWeeks();
    }

    public function render()
    {
        dd($this->tasks->pluck('task.name'));


        return view('livewire.project-controller.weekly-progress-board')->layout('project-controller.layouts.app');
    }
}
