<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PhcApproval;
use Illuminate\Support\Facades\Auth;
use App\Events\PhcApprovalUpdatedEvent;

class PhcApprovalModal extends Component
{
    public $show = false;
    public $approvalId;
    public $pin;
    public $error;

    public $notifications = [];

    protected $listeners = ['openValidationModal' => 'open'];

    public function open($approvalId)
    {
        $this->approvalId = $approvalId;
        $this->show = true;
        $this->error = null;
    }


    public function mount()
    {
        $this->notifications = Auth::user()
            ->unreadNotifications()
            ->take(10)
            ->get();
    }
    public function approve()
    {
        $approval = PhcApproval::with('user', 'phc.approvals')->findOrFail($this->approvalId);

        // Pastikan hanya user yang bersangkutan bisa approve
        if ($approval->user_id !== Auth::id()) {
            $this->error = 'Unauthorized.';
            return;
        }

        // Cek PIN (pakai kolom users.pin)
        if ($this->pin !== $approval->user->pin) {
            $this->error = 'Invalid PIN.';
            return;
        }

        // Update approval status
        $approval->update([
            'status' => 'approved',
            'validated_at' => now(),
        ]);

        $phc = $approval->phc;

        // Jika semua sudah approve, update status PHC
        if ($phc->approvals()->where('status', '!=', 'approved')->count() === 0) {
            $phc->update(['status' => 'ready']);
        }

        // Broadcast ke semua listener agar notifikasi dan status update
        event(new PhcApprovalUpdatedEvent($phc));

        $this->reset(['show', 'pin', 'error']);
        session()->flash('success', 'PHC Approved successfully!');
    }

    public function render()
    {
        return view('livewire.phc-approval-modal', [
            'notifications' => $this->notifications
        ]);
    }
}
