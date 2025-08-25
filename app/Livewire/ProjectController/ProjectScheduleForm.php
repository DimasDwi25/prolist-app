<?php

namespace App\Http\Livewire\ProjectController;

use Livewire\Component;
use App\Models\Project;
use App\Models\ProjectSchedule;
use App\Models\ProjectScheduleTask;
use App\Models\ProjectWeekSchedule;
use App\Models\Task;
use Illuminate\Support\Carbon;

class ProjectScheduleForm extends Component
{
    public Project $project;
    public ProjectSchedule $schedule;
    public array $tasks = [];

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->schedule = new ProjectSchedule();
        $this->addTask();
    }

    public function addTask()
    {
        $this->tasks[] = [
            'task_id' => '',
            'quantity' => '',
            'unit' => '',
            'bobot' => '',
            'plan_start' => '',
            'plan_finish' => '',
            'actual_start' => '',
            'actual_finish' => '',
            'order' => count($this->tasks) + 1,
            'weeks' => []
        ];
    }

    public function removeTask($index)
    {
        unset($this->tasks[$index]);
        $this->tasks = array_values($this->tasks);
    }

    public function addWeek($taskIndex)
    {
        $this->tasks[$taskIndex]['weeks'][] = [
            'week_number' => count($this->tasks[$taskIndex]['weeks']) + 1,
            'week_start' => '',
            'week_end' => '',
            'bobot_plan' => 0,
            'bobot_actual' => 0,
        ];
    }

    public function removeWeek($taskIndex, $weekIndex)
    {
        unset($this->tasks[$taskIndex]['weeks'][$weekIndex]);
        $this->tasks[$taskIndex]['weeks'] = array_values($this->tasks[$taskIndex]['weeks']);
    }

    public function save()
    {
        $this->validate([
            'schedule.name' => 'required|string|max:255',
        ]);

        $this->schedule->project_id = $this->project->pn_number;
        $this->schedule->save();

        foreach ($this->tasks as $taskData) {
            $task = ProjectScheduleTask::create([
                'project_schedule_id' => $this->schedule->id,
                'task_id' => $taskData['task_id'],
                'quantity' => $taskData['quantity'],
                'unit' => $taskData['unit'],
                'bobot' => $taskData['bobot'],
                'plan_start' => $taskData['plan_start'],
                'plan_finish' => $taskData['plan_finish'],
                'actual_start' => $taskData['actual_start'],
                'actual_finish' => $taskData['actual_finish'],
                'order' => $taskData['order']
            ]);

            foreach ($taskData['weeks'] as $week) {
                ProjectWeekSchedule::create([
                    'project_schedule_task_id' => $task->id,
                    'week_number' => $week['week_number'],
                    'week_start' => $week['week_start'],
                    'week_end' => $week['week_end'],
                    'bobot_plan' => $week['bobot_plan'],
                    'bobot_actual' => $week['bobot_actual']
                ]);
            }
        }

        session()->flash('success', 'Project Schedule berhasil dibuat.');
        return redirect()->route('engineer.project.show', $this->project->pn_number);
    }

    public function render()
    {
        return view('livewire.project-controller.project-schedule-form', [
            'allTasks' => Task::all()
        ]);
    }
}
