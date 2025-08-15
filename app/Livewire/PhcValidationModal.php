<?php

namespace App\Livewire;

use App\Events\PhcApprovalUpdatedEvent;
use App\Models\PHC;
use App\Models\User;
use Livewire\Component;
use App\Models\PhcApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PhcValidationModal extends Component
{
    public $showModal = false;
    public $approvalId;
    public $pin;

    protected $listeners = [
        'refreshValidationTable' => 'loadData',
        'removePmPcApprovals' => 'removePmPcApprovals',
        'openValidationModal' => 'open' // 👈 ini yang penting
    ];


    protected $rules = [
        'pin' => 'required|string|min:4',
    ];

    public function open($approvalId)
    {
        $this->approvalId = $approvalId;
        $this->pin = '';
        $this->showModal = true;
    }

    public function validatePin()
    {
        $this->validate();

        $user = auth()->user();

        if ($this->pin !== $user->pin) {
            $this->addError('pin', 'PIN salah!');
            return;
        }

        $approval = PhcApproval::find($this->approvalId);
        if (!$approval || $approval->user_id != $user->id) {
            $this->addError('pin', 'Data approval tidak valid!');
            return;
        }

        $phc = $approval->phc;

        // 🔹 1. Jika user adalah PM/PC/SuperAdmin & ho_engineering_id kosong
        if (in_array(strtolower($user->role->name), ['project manager', 'project controller', 'super_admin']) 
            && empty($phc->ho_engineering_id)) {

            $phc->update(['ho_engineering_id' => $user->id]);

            PhcApproval::where('phc_id', $phc->id)
                ->whereIn('user_id', User::whereHas('role', function ($q) {
                    $q->whereIn('name', ['project manager', 'project controller', 'super_admin']);
                })->pluck('id'))
                ->where('user_id', '!=', $user->id)
                ->delete();

            // 🔹 Kirim event untuk hapus approval di tabel user lain
            $this->dispatch('removePmPcApprovals', phcId: $phc->id);
        }

        // 🔹 2. Update approval user ini
        $approval->update([
            'status' => 'approved',
            'validated_at' => now(),
            'pin_hash' => bcrypt($this->pin)
        ]);

        // 🔹 3. Cek status ready
        $this->checkIfPhcReady($phc);

        // 🔹 4. Refresh tabel user ini
        $this->dispatch('refreshValidationTable');

        // 🔹 5. Refresh notifikasi
        $this->dispatch('refreshNotifications');

        $this->showModal = false;

        $this->dispatch('alert', [
            'type' => 'success',
            'message' => 'PHC berhasil divalidasi!'
        ]);
    }

    private function checkIfPhcReady($phc)
    {
        if (
            !empty($phc->ho_marketings_id) &&
            !empty($phc->pic_marketing_id) &&
            !empty($phc->ho_engineering_id)
        ) {
            $phc->update(['status' => 'ready']);
        }
    }




    public function approve($approvalId)
    {
        $approval = PhcApproval::findOrFail($approvalId);
        $approval->update(['status' => 'approved']);

        // Jika ho_engineering_id masih kosong dan role user termasuk PM/PC/SuperAdmin
        if (
            is_null($approval->phc->ho_engineering_id) &&
            in_array(strtolower($approval->user->role->name), ['project manager', 'project controller', 'super_admin'])
        ) {
            // Isi ho_engineering_id dengan user yang pertama approve
            $approval->phc->update(['ho_engineering_id' => $approval->user_id]);

            // Hapus semua approval lain dari 3 role ini
            PhcApproval::where('phc_id', $approval->phc_id)
                ->whereHas('user.role', function ($q) {
                    $q->whereIn('name', ['project manager', 'project controller', 'super_admin']);
                })
                ->where('id', '<>', $approval->id)
                ->delete();
        }

        // Cek apakah semua role wajib sudah approve → ubah status jadi ready
        $this->checkIfPhcReady($approval->phc);

        // Kirim event untuk update tabel real-time
        event(new PhcApprovalUpdatedEvent($approval->phc));

        return back()->with('success', 'Approval berhasil.');
    }




    public function render()
    {
        return view('livewire.phc-validation-modal');
    }
}
