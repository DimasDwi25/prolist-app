<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PhcApproval;

class PhcValidationTable extends Component
{
    public $approvals = [];

    protected $listeners = ['refreshValidationTable' => 'loadData'];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->approvals = PhcApproval::with('phc.project')
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->get();
    }

    public function openValidationModal($approvalId)
    {
        $this->dispatch('openValidationModal', approvalId: $approvalId);
    }

    public function render()
    {
        return view('livewire.phc-validation-table');
    }
}
