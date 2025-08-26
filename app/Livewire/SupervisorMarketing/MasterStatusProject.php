<?php

namespace App\Livewire\SupervisorMarketing;

use App\Exports\StatusProjectNewExport;
use App\Models\StatusProject;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MasterStatusProject extends Component
{
    use WithPagination;

    public $statusProjectId, $name, $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255|unique:status_projects,name',
    ];

    protected $paginationTheme = 'tailwind';

    public function save()
    {
        $this->validate();

        StatusProject::updateOrCreate(
            ['id' => $this->statusProjectId],
            ['name' => $this->name]
        );

        $this->resetForm();
        session()->flash('success', 'Status Project saved successfully!');
    }

    public function edit($id)
    {
        $status = StatusProject::findOrFail($id);
        $this->statusProjectId = $status->id;
        $this->name = $status->name;
        $this->isEditing = true;
    }

    public function delete($id)
    {
        StatusProject::destroy($id);
        session()->flash('success', 'Status Project deleted successfully!');
    }

    public function resetForm()
    {
        $this->reset(['statusProjectId', 'name', 'isEditing']);
    }

     public function export(): BinaryFileResponse
    {
        return Excel::download(new StatusProjectNewExport(), 'status_projects.xlsx');
    }

    public function render()
    {
        $user = auth()->user();
        $layout = match ($user->role->name) {
            'super_admin'              => 'admin.layouts.app',
            'marketing_director'       => 'marketing-director.layouts.app',
            'supervisor marketing'     => 'supervisor.layouts.app',
            'manager_marketing'        => 'supervisor.layouts.app',
            'sales_supervisor'         => 'supervisor.layouts.app',
            'marketing_admin'         => 'supervisor.layouts.app',
            'engineering_director'  => 'engineering_director.layouts.app',
        };
        return view('livewire.supervisor-marketing.master-status-project', [
            'statusProjects' => StatusProject::latest()->paginate(5),
        ])->layout('supervisor.layouts.app');
    }
}
