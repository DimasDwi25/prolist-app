<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Log;
use App\Models\PHC;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApprovallController extends Controller
{
    public function index(Request $request)
    {
        $query = Approval::where('user_id', $request->user()->id)
            ->with('approvable.project.phc', 'approvable.project');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $approvals = $query->latest()->get();
        return response()->json($approvals);
    }

    public function show(Request $request, $id)
    {
        $approval = Approval::where('user_id', $request->user()->id)
            ->with('approvable')
            ->findOrFail($id);

        return response()->json($approval);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:approved,rejected',
            'pin' => 'required|string',
        ]);

        $approval = Approval::where('user_id', $request->user()->id)
            ->with('user', 'approvable')
            ->findOrFail($id);

        $user = $approval->user;
        $pinDb = $user->pin; // <-- gunakan kolom 'pin' di table users

        if (str_starts_with($pinDb, '$2y$') && strlen($pinDb) === 60) {
            $isValid = Hash::check($request->pin, $pinDb);
        } else {
            $isValid = $request->pin === $pinDb;
        }

        // Jika valid, hash PIN di user supaya selanjutnya aman
        if (!$isValid) {
            return response()->json(['message' => 'PIN tidak valid'], 403);
        }

        if (!str_starts_with($pinDb, '$2y$') || strlen($pinDb) !== 60) {
            $user->pin = Hash::make($request->pin); // hash dari input user
            $user->save();
        }

        // Update status approval
        $approval->update([
            'status' => $request->status,
            'validated_at' => now(),
        ]);

        // Jika approval HO/validator pertama kali
        $phc = $approval->approvable;
        if ($approval->approvable_type === PHC::class && in_array($user->role->name, ['project manager', 'project controller', 'super_admin'])) {
            if (!$phc->ho_engineering_id) {
                $phc->update(['ho_engineering_id' => $user->id]);

                // Hapus semua approval validator lain yang masih pending
                Approval::where('approvable_type', PHC::class)
                    ->where('approvable_id', $phc->id)
                    ->where('status', 'pending')
                    ->where('user_id', '!=', $user->id)
                    ->delete();
            }
        }

        // Cek minimal 3 approval (HO Marketing + PIC Marketing + HO Engineering)
        $approvedCount = Approval::where('approvable_type', PHC::class)
            ->where('approvable_id', $phc->id)
            ->where('status', 'approved')
            ->count();

        if ($approvedCount >= 3) {
            $phc->update(['status' => 'ready']);
        }

        return response()->json([
            'message' => "Approval berhasil {$request->status}",
            'approval' => $approval,
            'phc_status' => $phc->status,
        ]);
    }

    // Update status approval WO
    public function updateStatusWo(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:approved,rejected',
            'pin' => 'required|string',
        ]);

        $approval = Approval::where('user_id', $request->user()->id)
            ->where('approvable_type', WorkOrder::class)
            ->with('user', 'approvable.project')
            ->findOrFail($id);

        $user = $approval->user;
        $pinDb = $user->pin;

        // cek PIN
        if (str_starts_with($pinDb, '$2y$') && strlen($pinDb) === 60) {
            $isValid = Hash::check($request->pin, $pinDb);
        } else {
            $isValid = $request->pin === $pinDb;
        }

        if (!$isValid) {
            return response()->json(['message' => 'PIN tidak valid'], 403);
        }

        // hash PIN jika belum di-hash
        if (!str_starts_with($pinDb, '$2y$') || strlen($pinDb) !== 60) {
            $user->pin = Hash::make($request->pin);
            $user->save();
        }

        // update status approval
        $approval->update([
            'status' => $request->status,
            'validated_at' => now(),
        ]);

        $wo = $approval->approvable;

        if ($request->status === 'approved') {
            // Hapus semua approval pending user lain untuk WO ini
            Approval::where('approvable_type', WorkOrder::class)
                ->where('approvable_id', $wo->id)
                ->where('status', 'pending')
                ->where('user_id', '!=', $user->id)
                ->delete();

            // Catat user yang approve di field approved_by
            $wo->update([
                'approved_by' => $user->id,
                'status' => 'approved',
            ]);
        } elseif ($request->status === 'rejected') {
            // Jika rejected, tetap tunggu approval lain
            $wo->update(['status' => 'waiting approval']);
        }

        return response()->json([
            'message' => "Approval berhasil {$request->status}",
            'approval' => $approval,
            'wo_status' => $wo->status,
            'approved_by' => $wo->approved_by,
        ]);
    }

    // Update status approval Log
    public function updateStatusLog(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:approved,rejected',
            'pin' => 'required|string',
        ]);

        $approval = Approval::where('user_id', $request->user()->id)
            ->where('approvable_type', Log::class)
            ->with('user', 'approvable.project')
            ->findOrFail($id);

        $user = $approval->user;
        $pinDb = $user->pin;

        // cek PIN
        if (str_starts_with($pinDb, '$2y$') && strlen($pinDb) === 60) {
            $isValid = Hash::check($request->pin, $pinDb);
        } else {
            $isValid = $request->pin === $pinDb;
        }

        if (!$isValid) {
            return response()->json(['message' => 'PIN tidak valid'], 403);
        }

        // hash PIN jika belum di-hash
        if (!str_starts_with($pinDb, '$2y$') || strlen($pinDb) !== 60) {
            $user->pin = Hash::make($request->pin);
            $user->save();
        }

        // update status approval
        $approval->update([
            'status' => $request->status,
            'validated_at' => now(),
        ]);

        $logs = $approval->approvable;

        return response()->json([
            'message' => "Approval berhasil {$request->status}",
            'approval' => $approval,
            'wo_status' => $logs->status,
            'approved_by' => $logs->approved_by,
        ]);
    }





}
