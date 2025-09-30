<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Approval;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LogController extends Controller
{
    // List logs by project_id
    public function index($project)
    {
        $logs = Log::with(['user', 'category', 'closer', 'responseUser', 'approvals'])
            ->where('project_id', $project)
            ->get();

        return response()->json($logs);
    }

    // Show specific log
    public function show($id)
    {
        $log = Log::with(['user', 'project', 'category', 'closer', 'responseUser', 'approvals'])->find($id);

        if (!$log) {
            return response()->json(['message' => 'Log not found'], 404);
        }

        return response()->json($log);
    }

    // Create new log
    public function store(Request $request)
{
    $userId = auth()->id(); // user yang create log
    $today = Carbon::today()->toDateString();

    // ambil project ID dari pn_number
    $project = Project::where('pn_number', $request->project_id)->firstOrFail();

    // cek 1 log per hari
    $exists = Log::where('users_id', $userId) // sesuai kolom di DB
        ->where('project_id', $project->pn_number)
        ->whereDate('tgl_logs', $today)
        ->exists();

    if ($exists) {
        return response()->json([
            'message' => 'User can only create 1 log per project per day'
        ], 422);
    }

    $validated = $request->validate([
        'categorie_log_id' => 'required|integer',
        'logs' => 'required|string',
        'tgl_logs' => 'required|date',
        'status' => 'nullable|string',
        'closing_date' => 'nullable|date',
        'closing_users' => 'nullable|integer',
        'response_by' => 'nullable|integer',
        'need_response' => 'nullable|boolean',
        'project_id' => 'required|exists:projects,pn_number',
    ]);

    $validated['users_id'] = $userId; // pakai users_id
    $validated['project_id'] = $project->pn_number; // pastikan ID numeric

    // Tentukan status otomatis
    if (!empty($validated['need_response']) && !empty($validated['response_by'])) {
        $validated['status'] = 'waiting approval';
    } else {
        $validated['status'] = 'open';
    }

    $log = Log::create($validated);

    // Jika need_response true, buat approval
    if (!empty($validated['need_response']) && !empty($validated['response_by'])) {
        $log->approvals()->create([
            'user_id' => $validated['response_by'],
            'type' => 'log',
            'status' => 'pending',
        ]);
    }

    return response()->json($log->load(['user', 'category', 'closer', 'responseUser', 'approvals']), 201);
}


    // Update log
    public function update(Request $request, $id)
    {
        $log = Log::find($id);

        if (!$log) {
            return response()->json(['message' => 'Log not found'], 404);
        }

        $validated = $request->validate([
            'categorie_log_id' => 'sometimes|integer',
            'users_id' => 'sometimes|integer',
            'logs' => 'sometimes|string',
            'tgl_logs' => 'sometimes|date',
            'status' => 'nullable|string',
            'closing_date' => 'nullable|date',
            'closing_users' => 'nullable|integer',
            'response_by' => 'nullable|integer',
            'need_response' => 'nullable|boolean',
            'project_id' => 'required|exists:projects,pn_number',
        ]);

        // Jika diupdate menjadi need_response true & response_by diisi
        if (!empty($validated['need_response']) && !empty($validated['response_by'])) {
            $validated['status'] = 'waiting approval';

            // Buat approval jika belum ada
            if ($log->approvals()->where('user_id', $validated['response_by'])->doesntExist()) {
                $log->approvals()->create([
                    'user_id' => $validated['response_by'],
                    'type' => 'log',
                    'status' => 'pending',
                ]);
            }
        }

        // Jika need_response false, status tetap open
        if (isset($validated['need_response']) && !$validated['need_response']) {
            $validated['status'] = 'open';
        }

        $log->update($validated);

        return response()->json($log->load(['user', 'category', 'closer', 'responseUser', 'approvals']));
    }

    // Delete log
    public function destroy($id)
    {
        $log = Log::find($id);

        if (!$log) {
            return response()->json(['message' => 'Log not found'], 404);
        }

        $log->delete();

        return response()->json(['message' => 'Log deleted']);
    }

    // Close a log
    public function close($id)
    {
        $log = Log::find($id);

        if (!$log) {
            return response()->json(['message' => 'Log not found'], 404);
        }

        // Update status menjadi 'closed'
        $log->update(['status' => 'closed']);

        return response()->json([
            'message' => 'Log has been closed',
            'log' => $log->load(['user', 'category', 'responseUser', 'approvals']),
        ]);
    }


}
