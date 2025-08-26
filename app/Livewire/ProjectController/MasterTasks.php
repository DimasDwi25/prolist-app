<?php

namespace App\Livewire\ProjectController;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Task;

class MasterTasks extends Component
{
    use WithPagination;

    public $taskId;
    public $taskName;
    public $description;
    public $isEdit = false;
    public $search = '';


    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'taskName' => 'required|string|max:255',
        'description' => 'nullable|string',
    ];

    // Reset ke halaman 1 saat search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $tasks = Task::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('task_name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $user = auth()->user();

        if ($user->role->name === 'engineering_director') {
            $layout = 'engineering_director.layouts.app';
        } elseif ($user->role->name === 'project-controller') {
            $layout = 'project-controller.layouts.app';
        } else {
            $layout = 'layouts.app'; // fallback default
        }

        return view('livewire.project-controller.master-tasks', compact('tasks'))
            ->layout($layout);
    }

    public function resetForm()
    {
        $this->taskId = null;
        $this->taskName = '';
        $this->description = '';
        $this->isEdit = false;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEdit) {
            Task::where('id', $this->taskId)->update([
                'task_name' => $this->taskName,
                'description' => $this->description,
            ]);
        } else {
            Task::create([
                'task_name' => $this->taskName,
                'description' => $this->description,
            ]);
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $this->taskId = $task->id;
        $this->taskName = $task->task_name;
        $this->description = $task->description;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        Task::findOrFail($id)->delete();
        $this->resetForm();
    }
}
