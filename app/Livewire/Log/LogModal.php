<?php

namespace App\Livewire\Log;

use App\Models\CategorieLog;
use App\Models\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LogModal extends Component
{
    public $projectId;
    public $logId = null; // <-- untuk edit
    public $tgl_logs, $categorie_log_id, $logs, $askApproval = false, $response_by, $status = 'open';
    public $categories = [], $users = [];
    public $showModal = false;

    protected $listeners = [
        'openLogModal' => 'open',
        'closeLogModal' => 'close',
        'editLog' => 'edit',   // <-- listener untuk edit
    ];

    protected $rules = [
        'tgl_logs' => 'required|date',
        'categorie_log_id' => 'required|exists:categorie_logs,id',
        'logs' => 'required|string|min:5',
        'status' => 'required|in:open,close,waiting approval,approved',
        'response_by' => 'nullable|exists:users,id',
    ];

    public function mount($projectId)
    {
        $this->projectId = $projectId;
        $this->categories = CategorieLog::all();
        $this->users = User::all();
    }

    public function open()
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function close()
    {
        $this->showModal = false;
    }

    public function resetInput()
    {
        $this->logId = null;
        $this->tgl_logs = null;
        $this->categorie_log_id = null;
        $this->logs = null;
        $this->askApproval = false;
        $this->response_by = null;
        $this->status = 'open';
    }

    public function save()
    {
        $this->validate();

        if ($this->logId) {
            // Update log
            $log = Log::findOrFail($this->logId);

            // Pastikan hanya pemilik log yang bisa edit
            if ((int)$log->users_id !== (int)Auth::id()) {
                abort(403, 'Anda tidak boleh mengedit log ini.');
            }

            $log->update([
                'tgl_logs'     => $this->tgl_logs,
                'categorie_log_id'  => $this->categorie_log_id,
                'logs'         => $this->logs,
                'status'       => $this->status,
                'response_by'  => $this->response_by,
            ]);

            session()->flash('message', 'Log berhasil diperbarui!');
        } else {
            // Cek apakah user sudah input log untuk hari ini di project yang sama
            $exists = Log::where('project_id', $this->projectId)
                ->where('users_id', Auth::id())
                ->whereDate('tgl_logs', now()->toDateString())
                ->exists();

            if ($exists) {
                $this->addError('tgl_logs', 'Anda sudah mengisi log untuk hari ini di project ini.');
                return;
            }

            // Tentukan status otomatis
            $status = $this->askApproval && $this->response_by 
                ? 'waiting approval' 
                : 'open';

            Log::create([
                'project_id'   => $this->projectId,
                'tgl_logs'     => $this->tgl_logs,
                'categorie_log_id'  => $this->categorie_log_id,
                'logs'         => $this->logs,
                'status'       => $status,
                'response_by'  => $this->response_by,
                'users_id'     => Auth::id(),
            ]);

            session()->flash('message', 'Log berhasil ditambahkan!');
        }

        $this->close();
        $this->dispatch('logSaved');
    }

    public function editLog($id)
    {
        $log = Log::findOrFail($id);

        $this->tgl_logs        = $log->tgl_logs;
        $this->categorie_log_id = $log->categorie_log_id;
        $this->logs            = $log->logs;
        $this->response_by     = $log->response_by;
        $this->status          = $log->status;

        $this->showModal = true;
    }



    public function render()
    {
        return view('livewire.log.log-modal');
    }
}
