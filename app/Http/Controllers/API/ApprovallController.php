<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApprovallController extends Controller
{
    //
    /**
     * List semua approval milik user.
     * Bisa filter dengan query ?status=pending/approved/rejected
     */
    public function index(Request $request)
    {
        $query = Approval::where('user_id', $request->user()->id)
            ->with('approvable.project');

        // filter status jika ada query param
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $approvals = $query->latest()->get();

        return response()->json($approvals);
    }

    /**
     * Tampilkan detail approval milik user.
     */
    public function show(Request $request, $id)
    {
        $approval = Approval::where('user_id', $request->user()->id)
            ->with('approvable')
            ->findOrFail($id);

        return response()->json($approval);
    }

    /**
     * Update status approval dengan PIN.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:approved,rejected',
            'pin'    => 'required|string',
        ]);

        // Pastikan approval milik user
        $approval = Approval::where('user_id', $request->user()->id)
            ->with('user', 'approvable')
            ->findOrFail($id);

        $user = $approval->user;

        // Cek PIN
        if (!Hash::check($request->pin, $user->pin_hash)) {
            return response()->json(['message' => 'PIN tidak valid'], 403);
        }

        // Update status dan validated_at
        $approval->update([
            'status'       => $request->status,
            'validated_at' => now(),
        ]);

        return response()->json([
            'message'  => "Approval berhasil {$request->status}",
            'approval' => $approval,
        ]);
    }
}
