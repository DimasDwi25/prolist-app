<?php

namespace App\Livewire\ProjectController;

use App\Models\ScopeOfWork;
use Livewire\Component;

class ViewSow extends Component
{
    public $phcId;
    public $description = '';
    public $category = '';
    public $items = '';
    public $editingId = null;
    public $sows = [];

    public $showModal = false;

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

    public function loadSows()
    {
        $this->sows = ScopeOfWork::where('phc_id', $this->phcId)
            ->orderBy('created_at', 'desc')
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
        return view('livewire.project-controller.view-sow');
    }
}
