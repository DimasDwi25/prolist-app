<?php

namespace App\Livewire\ProjectController;

use App\Models\ScopeOfWork;
use Livewire\Component;
use Livewire\WithPagination;

class MasterScopeOfWork extends Component
{
    use WithPagination;

    public $scopeOfWorkId, $names, $description, $isEditing = false;

    protected $rules = [
        'names' => 'required|string|max:255',
        'description' => 'nullable|string',
    ];

    protected $paginationTheme = 'tailwind'; // agar cocok dengan Tailwind

    public function updating($property)
    {
        if ($property === 'search') {
            $this->resetPage(); // reset ke halaman 1 saat pencarian berubah
        }
    }

    public function save()
    {
        $this->validate();

        ScopeOfWork::updateOrCreate(
            ['id' => $this->scopeOfWorkId],
            [
                'names' => $this->names,
                'description' => $this->description,
            ]
        );

        $this->resetForm();
        session()->flash('success', 'Scope of Work saved successfully!');
    }

    public function edit($id)
    {
        $sow = ScopeOfWork::findOrFail($id);
        $this->scopeOfWorkId = $sow->id;
        $this->names = $sow->names;
        $this->description = $sow->description;
        $this->isEditing = true;
    }

    public function delete($id)
    {
        ScopeOfWork::destroy($id);
    }

    public function resetForm()
    {
        $this->reset(['scopeOfWorkId', 'names', 'description', 'isEditing']);
    }

    public function render()
    {
        $user = auth()->user();

        if ($user->role->name === 'engineering_director') {
            $layout = 'engineering_director.layouts.app';
        } elseif ($user->role->name === 'project controller') {
            $layout = 'project-controller.layouts.app';
        } else {
            $layout = 'layouts.app'; // fallback default
        }
        return view('livewire.project-controller.master-scope-of-work', [
            'scopeOfWorks' => ScopeOfWork::latest()->paginate(5),
        ])->layout($layout);
    }
}
