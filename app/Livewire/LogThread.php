<?php

namespace App\Livewire;

use App\Events\LogClosed;
use App\Events\LogUpdated;
use App\Models\Log;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;

class LogThread extends Component
{
    public Project $project;
    public string $logContent = '';
    public $categoryId;
    public $editingLogId = null;

    public $need_response = false;
    public $responseBy = null;

    protected $casts = [
        'need_response' => 'boolean',
    ];

    protected $listeners = ['refreshLogs' => '$refresh']; // Untuk future: real-time Echo

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function saveLog()
    {
        $today = now()->toDateString();

        $exists = Log::where('users_id', Auth::id())
            ->whereDate('tgl_logs', $today)
            ->where('project_id', $this->project->id)
            ->exists();

        if ($exists) {
            $this->dispatch('log-error', message: 'Kamu hanya bisa mengisi log satu kali per hari.');
            return;
        }

        $this->validate([
            'logContent' => 'required|string|min:5',
            'categoryId' => 'required|exists:categorie_logs,id',
            'responseBy' => $this->need_response ? 'required|exists:users,id' : 'nullable',
        ]);

        $log = Log::create([
            'categorie_log_id' => $this->categoryId,
            'users_id' => Auth::id(),
            'logs' => $this->logContent,
            'tgl_logs' => now(),
            'status' => 'open',
            'project_id' => $this->project->id,
            'need_response' => $this->need_response,
            'response_by' => $this->need_response ? $this->responseBy : null,
        ]);

        // Untuk real-time broadcast (jika nanti pakai Laravel Echo + Pusher)
        event(new \App\Events\LogCreated($log));

        $this->reset(['logContent', 'categoryId', 'need_response', 'responseBy']);

        $this->dispatch('log-success', message: 'Log berhasil disimpan.');
    }

    public function editLog($id)
    {
        $log = Log::findOrFail($id);
        if ($log->users_id !== Auth::id()) {
            abort(403);
        }
        if ($log->status === 'close') {
            $this->dispatch('log-error', message: 'Log sudah ditutup dan tidak dapat diedit.');
            return;
        }

        $this->editingLogId = $log->id;
        $this->logContent = $log->logs;
        $this->categoryId = $log->categorie_log_id;
        $this->need_response = $log->need_response;
        $this->responseBy = $log->response_by;
    }

    public function updateLog()
    {
        $this->validate([
            'logContent' => 'required|string|min:5',
            'categoryId' => 'required|exists:categorie_logs,id',
            'responseBy' => $this->need_response ? 'required|exists:users,id' : 'nullable',
        ]);

        $log = Log::findOrFail($this->editingLogId);
        if ($log->users_id !== Auth::id()) {
            abort(403);
        }

        $log->update([
            'categorie_log_id' => $this->categoryId,
            'logs' => $this->logContent,
            'need_response' => $this->need_response,
            'response_by' => $this->need_response ? $this->responseBy : null,
        ]);
        event(new LogUpdated($log));

        $this->reset(['logContent', 'categoryId', 'need_response', 'responseBy', 'editingLogId']);
        $this->dispatch('log-success', message: 'Log berhasil diperbarui.');
    }

    public function closeLog($id)
    {
        $log = Log::findOrFail($id);

        if ($log->users_id !== Auth::id()) {
            abort(403);
        }

        $log->update([
            'status' => 'close',
            'closing_date' => now(),
            'closing_users' => Auth::id(),
        ]);

        event(new LogClosed($log));

        $this->dispatch('log-success', message: 'Log berhasil ditutup.');
    }


    public function render()
    {
        $logs = Log::with(['user', 'category', 'responseUser'])
            ->where('project_id', $this->project->id)
            ->orderByDesc('tgl_logs')
            ->paginate(5);

        return view('livewire.log-thread', compact('logs'));
    }
}
