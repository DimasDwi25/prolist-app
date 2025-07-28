<?php

namespace App\Livewire\ProjectController;

use App\Models\Project;
use App\Models\ProjectSchedule;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectSchedules extends Component
{
    use WithPagination;

    public $scheduleId;
    public $project_id;
    public $plan_start;
    public $plan_finish;
    public $actual_date;
    public $actual_finish;
    public $bobot = 100;
    public $plan_progress = 0;
    public $actual_progress = 0;

    public $isEdit = false;
    public $search = '';

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'project_id' => 'required|exists:projects,id',
        'plan_start' => 'required|date',
        'plan_finish' => 'required|date|after_or_equal:plan_start',
        'actual_date' => 'nullable|date',
        'actual_finish' => 'nullable|date|after_or_equal:actual_date',
        'bobot' => 'numeric|min:0|max:100',
        'plan_progress' => 'numeric|min:0|max:100',
        'actual_progress' => 'numeric|min:0|max:100',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $schedules = ProjectSchedule::with('project')
            ->when($this->search, function ($query) {
                $query->whereHas('project', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.project-controller.project-schedules', [
            'schedules' => $schedules,
            'projects' => Project::all(),
        ])->layout('project-controller.layouts.app');
    }

    public function resetForm()
    {
        $this->scheduleId = null;
        $this->project_id = '';
        $this->plan_start = '';
        $this->plan_finish = '';
        $this->actual_date = '';
        $this->actual_finish = '';
        $this->bobot = 100;
        $this->plan_progress = 0;
        $this->actual_progress = 0;
        $this->isEdit = false;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEdit) {
            ProjectSchedule::where('id', $this->scheduleId)->update($this->getData());
        } else {
            ProjectSchedule::create($this->getData());
        }

        session()->flash('message', $this->isEdit ? 'Schedule updated successfully.' : 'Schedule added successfully.');

        $this->resetForm();
    }

    private function getData()
    {
        return [
            'project_id' => $this->project_id,
            'plan_start' => $this->plan_start,
            'plan_finish' => $this->plan_finish,
            'actual_date' => $this->actual_date,
            'actual_finish' => $this->actual_finish,
            'bobot' => $this->bobot,
            'plan_progress' => $this->plan_progress,
            'actual_progress' => $this->actual_progress,
        ];
    }

    public function edit($id)
    {
        $schedule = ProjectSchedule::findOrFail($id);
        $this->scheduleId = $schedule->id;
        $this->project_id = $schedule->project_id;
        $this->plan_start = $schedule->plan_start;
        $this->plan_finish = $schedule->plan_finish;
        $this->actual_date = $schedule->actual_date;
        $this->actual_finish = $schedule->actual_finish;
        $this->bobot = $schedule->bobot;
        $this->plan_progress = $schedule->plan_progress;
        $this->actual_progress = $schedule->actual_progress;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        ProjectSchedule::findOrFail($id)->delete();
        session()->flash('message', 'Schedule deleted successfully.');
        $this->resetForm();
    }
}
