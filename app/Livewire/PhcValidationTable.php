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
        $this->approvals = PhcApproval::with(['phc', 'user'])
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            // Tambahkan kondisi untuk exclude jika ho_engineering sudah diisi
            ->where(function ($query) {
                $query->whereHas('phc', function ($q) {
                    $q->whereNull('ho_engineering_id')
                    ->orWhereColumn('ho_engineering_id', 'user_id');
                })->orWhereHas('user', function ($q) {
                    $q->whereHas('role', function ($r) {
                        $r->whereNotIn('name', ['project manager', 'project controller', 'super_admin']);
                    });
                });
            })

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
