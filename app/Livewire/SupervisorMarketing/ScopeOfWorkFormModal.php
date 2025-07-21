<?php

namespace App\Livewire\SupervisorMarketing;

use App\Models\ScopeOfWork;
use Livewire\Component;

class ScopeOfWorkFormModal extends Component
{
    public $phcId;
    public $description = '';
    public $category = '';
    public $items = '';
    public $editingId = null;
    public $sows = [];

    public $showModal = false; // Untuk kontrol modal

    protected $listeners = ['openModal' => 'open', 'closeModal' => 'close', 'delete' => 'delete'];

    protected $rules = [
        'description' => 'required|string',
        'category' => 'required|string',
        'items' => 'required|string',
    ];

    public function mount($phcId)
    {
        $this->phcId = $phcId;
        $this->loadSows();
    }

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
        $this->sows = ScopeOfWork::where('phc_id', $this->phcId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function save()
    {
        $this->validate();

        ScopeOfWork::updateOrCreate(
            ['id' => $this->editingId],
            [
                'phc_id' => $this->phcId,
                'description' => $this->description,
                'category' => $this->category,
                'items' => $this->items,
            ]
        );

        $this->reset(['description', 'category', 'items', 'editingId']);
        $this->loadSows();
    }

    public function edit($id)
    {
        $sow = ScopeOfWork::findOrFail($id);
        $this->editingId = $sow->id;
        $this->description = $sow->description;
        $this->category = $sow->category;
        $this->items = $sow->items;
    }

    public function delete($id)
    {
        ScopeOfWork::findOrFail($id)->delete();
        $this->loadSows();
    }

    public function render()
    {
        return view('livewire.supervisor-marketing.scope-of-work-form-modal');
    }
}
