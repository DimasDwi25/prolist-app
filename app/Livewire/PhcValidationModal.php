<?php

namespace App\Livewire;

use App\Models\PHC;
use App\Models\User;
use Livewire\Component;
use App\Models\PhcApproval;
use Illuminate\Support\Facades\Hash;

class PhcValidationModal extends Component
{
    public $showModal = false;
    public $approvalId;
    public $pin;

    protected $listeners = ['openValidationModal' => 'open'];

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
        if ($approval && $approval->user_id == $user->id) {
            $approval->update(['status' => 'approved']);
        }

        // Tambahkan ini untuk update status PHC jika semua user sudah approve
        $this->phcId = $approval->phc_id; // tambahkan ini agar bisa dipakai di method approve()
        $this->approve(); // panggil fungsi approve()

        $this->showModal = false;
        $this->dispatch('refreshValidationTable');
        $this->dispatch('refreshNotifications');
        $this->dispatch('alert', [
            'type' => 'success',
            'message' => 'PHC berhasil divalidasi!'
        ]);
    }


    public function approve()
    {
        $user = auth()->user();

        // Simpan approval user
        PhcApproval::updateOrCreate(
            ['phc_id' => $this->phcId, 'user_id' => $user->id],
            ['status' => 'approved']
        );

        // Cek apakah semua validator sudah approve
        $phcId = $this->phcId;
        $requiredRoles = [
            'supervisor engineer',
            'project controller',
            'supervisor marketing',
            'project manager',
        ];

        // Ambil user id yang punya role validator
        $validatorIds = User::join('roles', 'roles.id', '=', 'users.role_id')
            ->whereIn('roles.name', $requiredRoles)
            ->pluck('users.id');


        // Cek jumlah approval yang sudah approve untuk PHC ini
        $approvedCount = PhcApproval::where('phc_id', $phcId)
            ->where('status', 'approved')
            ->whereIn('user_id', $validatorIds)
            ->count();

        if ($approvedCount === count($requiredRoles)) {
            // Update status PHC menjadi ready
            $phc = PHC::find($phcId);
            if ($phc && $phc->status !== 'ready') {
                $phc->status = 'ready';
                $phc->save();
            }
        }

        session()->flash('success', 'PHC approved successfully.');
    }


    public function render()
    {
        return view('livewire.phc-validation-modal');
    }
}
