<?php

namespace App\Livewire\SupervisorMarketing;

use App\Models\Project;
use App\Models\ScopeOfWork;
use App\Models\ScopeOfWorkProject;
use Livewire\Component;

class ScopeOfWorkFormModal extends Component
{
    public $projectId;
    public $scope_of_work_id;
    public $description;

    public $projects = [];
    public $scopeOfWorks = [];
    public $editingId = null; // ID dari scope_of_work_project
    public $sows = [];

    public $showModal = false;

    protected $listeners = ['openModal' => 'open', 'closeModal' => 'close', 'delete' => 'delete'];

    public function mount($projectId)
    {
        $this->projectId = $projectId;
        $this->projects = Project::all(); // Bisa dihapus jika tak dipakai
        $this->scopeOfWorks = ScopeOfWork::all();
        $this->loadSows(); // Panggil untuk load SOW langsung saat buka modal
    }

    protected $rules = [
        'projectId' => 'required|exists:projects,id',
        'scope_of_work_id' => 'required|exists:scope_of_works,id',
        'description' => 'required|string',
    ];

    public function open()
    {
        $this->showModal = true;
    }
    public function close()
    {
        $this->showModal = false;
    }

    public function loadSows()
    {
        $this->sows = ScopeOfWorkProject::with('scopeOfWork')
            ->where('project_id', $this->projectId)
            ->latest()
            ->get();
    }

    public function save()
    {
        $this->validate();

        ScopeOfWorkProject::create([
            'project_id' => $this->projectId,
            'scope_of_work_id' => $this->scope_of_work_id,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Scope of Work Project berhasil ditambahkan!');
        $this->reset(['scope_of_work_id', 'description']);
        $this->loadSows();
    }

    public function edit($id)
    {
        $sowProject = ScopeOfWorkProject::with('scopeOfWork')->findOrFail($id);
        $this->editingId = $sowProject->id;
        $this->description = $sowProject->description;
        $this->category = $sowProject->scopeOfWork->name ?? '';
        $this->items = ''; // Anda bisa menambah kolom items di pivot jika perlu
    }

    public function delete($id)
    {
        ScopeOfWorkProject::findOrFail($id)->delete();
        $this->loadSows();
    }

    public function render()
    {
        return view('livewire.supervisor-marketing.scope-of-work-form-modal');
    }
}
