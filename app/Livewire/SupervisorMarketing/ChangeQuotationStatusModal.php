<?php

namespace App\Livewire\SupervisorMarketing;

use App\Models\Quotation;
use Livewire\Component;

class ChangeQuotationStatusModal extends Component
{
    public $quotationId;
    public $status;

    public $statusOptions = [
        'A' => '✓ Completed',
        'D' => '⏳ No PO Yet',
        'E' => '❌ Cancelled',
        'F' => '⚠️ Lost Bid',
        'O' => '🕒 On Going',
    ];

    public function openStatusModal($id)
    {
        $quotation = Quotation::findOrFail($id);
        $this->quotationId = $quotation->id;
        $this->status = $quotation->status;

        // trigger AlpineJS untuk buka modal
        $this->dispatch('open-status-modal-browser');
    }

    public function updateStatus()
    {
        $this->validate(['status' => 'required|in:A,D,E,F,O']);

        Quotation::findOrFail($this->quotationId)->update(['status' => $this->status]);

        $this->dispatch('close-status-modal');
        $this->dispatch('refreshDatatable');
        session()->flash('success', 'Status updated successfully.');
    }

    public function render()
    {
        return view('livewire.supervisor-marketing.change-quotation-status-modal');
    }
}
