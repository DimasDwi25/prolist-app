<?php

namespace App\Livewire\ProjectController;

use App\Models\ManPowerAllocation;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ManPowerAllocationForm extends Component
{
    use WithPagination;

    public $project, $allocationId, $project_id, $user_id, $role_id, $isEditing = false;
    public $search = '';
    public $fixedProjectId; // ini untuk pn_number yang dikirim dari show()

    protected $rules = [
        'project_id' => 'required|exists:projects,pn_number',
        'user_id'    => 'required|exists:users,id',
        'role_id'    => 'required|exists:roles,id',
    ];

    protected $paginationTheme = 'tailwind';

    public function mount(Project $project)
    {
        $this->project_id = $project->pn_number;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function save()
    {
        $this->validate();

        ManPowerAllocation::updateOrCreate(
            ['id' => $this->allocationId],
            [
                'project_id' => $this->project_id, // ini pn_number
                'user_id'    => $this->user_id,
                'role_id'    => $this->role_id,
            ]
        );

        $this->resetForm();
        session()->flash('success', 'Man Power Allocation saved successfully!');
    }

    public function edit($id)
    {
        $allocation = ManPowerAllocation::findOrFail($id);
        $this->allocationId = $allocation->id;
        $this->project_id   = $allocation->project_id;
        $this->user_id      = $allocation->user_id;
        $this->role_id      = $allocation->role_id;
        $this->isEditing    = true;
    }

    public function delete($id)
    {
        ManPowerAllocation::destroy($id);
    }

    public function resetForm()
    {
        $this->reset(['allocationId', 'user_id', 'role_id', 'isEditing']);
        $this->project_id = $this->project->pn_number;
    }

    public function render()
    {
        $allocations = ManPowerAllocation::with(['project', 'user', 'role'])
            ->where('project_id', $this->project_id)
            ->whereHas('project', fn($q) => $q->where('pn_number', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(5);

        $user = auth()->user();

        if ($user->role->name === 'engineering_director') {
            $layout = 'engineering_director.layouts.app';
        } elseif ($user->role->name === 'project-controller') {
            $layout = 'project-controller.layouts.app';
        } else {
            $layout = 'layouts.app'; // fallback default
        }

        return view('livewire.project-controller.man-power-allocation-form', [
            'allocations' => $allocations,
            'users' => User::whereHas('role', function ($q) {
                $q->whereIn('name', [
                    'engineer',
                    'engineering_supervisor',
                    'project manager',
                    'project controller',
                    'engineering_admin',
                    'electrician'
                ]);
            })->with('role')->get(),

            'roles' => Role::where('type_role', 2)->get(),
            'projects' => [], // nggak perlu dropdown
        ])->layout($layout);
    }
}
