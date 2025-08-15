<?php

namespace App\Livewire\SupervisorMarketing;

use App\Models\Quotation;
use Livewire\Component;

class ChangeQuotationStatusModal extends Component
{
    public $quotationNumber;  // Changed from $quotationId to $quotationNumber
    public $status;

    public $statusOptions = [
        'A' => '[A] ✓ Completed',
        'D' => '[D] ⏳ No PO Yet',
        'E' => '[E] ❌ Cancelled',
        'F' => '[F] ⚠️ Lost Bid',
        'O' => '[O] 🕒 On Going',
    ];

    public function openStatusModal($quotationNumber)  // Changed parameter name
    {
        $quotation = Quotation::where('quotation_number', $quotationNumber)->firstOrFail();
        $this->quotationNumber = $quotation->quotation_number;  // Using the correct key
        $this->status = $quotation->status;

        $this->dispatch('open-status-modal-browser');
    }

    public function updateStatus()
    {
        $this->validate(['status' => 'required|in:A,D,E,F,O']);

        Quotation::where('quotation_number', $this->quotationNumber)  // Updated query
                ->firstOrFail()
                ->update(['status' => $this->status]);

        $this->dispatch('close-status-modal');
        $this->dispatch('refreshDatatable');
        session()->flash('success', 'Status updated successfully.');
    }

    public function render()
    {
        return view('livewire.supervisor-marketing.change-quotation-status-modal');
    }
}