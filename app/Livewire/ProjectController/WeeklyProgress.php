<?php

namespace App\Livewire\ProjectController;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Project;
use App\Models\ProjectSchedule;
use App\Models\ProjectScheduleTask;
use App\Models\ProjectWeekSchedule;

class WeeklyProgress extends Component
{
    public Project $project;
    public ProjectSchedule $schedule;
    public ProjectScheduleTask $task;

    public $weeks = [];

    public function mount(Project $project, ProjectSchedule $schedule, ProjectScheduleTask $task)
    {
        $this->project = $project;
        $this->schedule = $schedule;
        $this->task = $task;

        $this->generateWeeksDynamic();
        $this->loadWeeks();
    }

    public function generateWeeksDynamic()
    {
        // Hanya generate week jika actual_start ada
        $startDate = $this->task->actual_start;
        if (!$startDate) {
            return; // Tidak buat minggu kalau belum ada tanggal actual_start
        }

        $endDate = $this->task->actual_finish ?? now();

        $start = Carbon::parse($startDate)->startOfWeek(Carbon::MONDAY);
        $end = Carbon::parse($endDate)->endOfWeek(Carbon::SUNDAY);

        $lastWeek = $this->task->weeks()->orderByDesc('week_number')->first();
        $weekNumber = $lastWeek ? $lastWeek->week_number + 1 : 1;
        $start = $lastWeek
            ? Carbon::parse($lastWeek->week_start)->addWeek()
            : $start;

        if (!$lastWeek) {
            $this->task->weeks()->create([
                'week_number' => 1,
                'week_start' => $start,
                'week_end' => $start->copy()->endOfWeek(Carbon::SUNDAY),
                'bobot_plan' => 0,
                'bobot_actual' => 0,
            ]);
        }

        if ($this->task->actual_finish) {
            while ($start <= $end) {
                $this->task->weeks()->firstOrCreate([
                    'week_number' => $weekNumber
                ], [
                    'week_start' => $start,
                    'week_end' => $start->copy()->endOfWeek(Carbon::SUNDAY),
                    'bobot_plan' => 0,
                    'bobot_actual' => 0,
                ]);
                $start->addWeek();
                $weekNumber++;
            }
        }
    }


    public function loadWeeks()
    {
        $this->weeks = $this->task->weeks()
            ->orderBy('week_number')
            ->get()
            ->toArray();
    }

    public function updateBobot($weekId, $field, $value)
    {
        $week = ProjectWeekSchedule::find($weekId);
        if (!$week)
            return;

        $numericValue = is_numeric($value) ? $value : 0;
        $week->update([$field => $numericValue]);

        $lastWeek = $this->task->weeks()->max('week_number');
        if ($week->week_number == $lastWeek && !$this->task->actual_finish) {
            $this->task->weeks()->create([
                'week_number' => $lastWeek + 1,
                'week_start' => Carbon::parse($week->week_start)->addWeek(),
                'week_end' => Carbon::parse($week->week_start)->addWeek()->endOfWeek(Carbon::SUNDAY),
                'bobot_plan' => 0,
                'bobot_actual' => 0,
            ]);
        }

        $this->loadWeeks();
    }

    /** Hapus minggu tertentu */
    public function deleteWeek($weekId)
    {
        $week = ProjectWeekSchedule::find($weekId);
        if ($week) {
            $week->delete();
        }
        $this->loadWeeks();
    }

    public function render()
    {
        return view('livewire.project-controller.weekly-progress', [
            'weeks' => $this->weeks,
            'task' => $this->task,
        ])->layout('project-controller.layouts.app');
    }
}
