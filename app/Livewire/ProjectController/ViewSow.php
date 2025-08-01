<?php

namespace App\Livewire\ProjectController;

use App\Models\Project;
use App\Models\ScopeOfWork;
use App\Models\ScopeOfWorkProject;
use Livewire\Component;

class ViewSow extends Component
{
    public $projectId;
    public $sowProjects = [];
    public $editingId = null;
    public $sows = [];

    public $showModal = false;

    // protected $rules = [
    //     'description' => 'required|string',
    //     'category' => 'required|string',
    //     'items' => 'required|string',
    // ];

    public function mount($projectId)
    {
        $this->projectId = $projectId;
        $this->loadSows();
    }

    public function loadSows()
    {
        $this->sowProjects = ScopeOfWorkProject::with(['scopeOfWork', 'project'])
            ->where('project_id', $this->projectId)
            ->get();
    }

    public function open()
    {
        $this->showModal = true;
    }

    public function close()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.project-controller.view-sow', [
            'project' => Project::findOrFail($this->projectId),
        ]);
    }
}
