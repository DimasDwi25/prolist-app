<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PhcApproval;
use App\Models\PHC;
use App\Models\User;
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
        $approval = PhcApproval::with(['phc', 'user.role'])->findOrFail($this->approvalId);

        // Validasi otorisasi dan PIN
        if ($approval->user_id !== Auth::id()) {
            $this->error = 'Unauthorized.';
            return;
        }

        if ($this->pin !== Auth::user()->pin) {
            $this->error = 'Invalid PIN.';
            return;
        }

        $phc = $approval->phc;
        $currentUser = Auth::user();

        // Jika user termasuk dari 3 role khusus (PM/PC/SuperAdmin) dan ho_engineering_id belum diisi
        if (
            in_array($currentUser->role->name, ['project manager', 'project controller', 'super_admin'])
            && !$phc->ho_engineering_id
        ) {

            // Update ho_engineering_id di tabel PHC
            $phc->update(['ho_engineering_id' => $currentUser->id]);

            // Hapus semua approval pending untuk 3 role ini
            PhcApproval::where('phc_id', $phc->id)
                ->where('status', 'pending')
                ->whereHas('user', function ($q) {
                    $q->whereHas('role', function ($r) {
                        $r->whereIn('name', ['project manager', 'project controller', 'super_admin']);
                    });
                })
                ->delete();
        }

        // Update status approval
        $approval->update([
            'status' => 'approved',
            'validated_at' => now()
        ]);

        // Cek jika sudah 3 validasi lengkap
        $this->checkCompleteValidations($phc);

        $this->reset(['show', 'pin', 'error']);
        session()->flash('success', 'PHC berhasil divalidasi!');
        $this->dispatch('refreshValidationTable');
    }

    protected function checkCompleteValidations($phc)
    {
        // 3 validasi yang dibutuhkan:
        // 1. HO Marketing
        // 2. PIC Marketing
        // 3. HO Engineering (yang sudah diisi oleh PM/PC/SuperAdmin pertama)

        $requiredApprovals = [
            $phc->ho_marketings_id,
            $phc->pic_marketing_id,
            $phc->ho_engineering_id
        ];

        // Hitung yang sudah di-approve
        $approvedCount = PhcApproval::where('phc_id', $phc->id)
            ->whereIn('user_id', array_filter($requiredApprovals))
            ->where('status', 'approved')
            ->count();

        // Jika 3 validasi sudah lengkap
        if ($approvedCount === 3) {
            $phc->update(['status' => 'ready']);
            event(new PhcApprovalUpdatedEvent($phc));
        }
    }

    public function render()
    {
        return view('livewire.phc-approval-modal', [
            'notifications' => $this->notifications
        ]);
    }
}