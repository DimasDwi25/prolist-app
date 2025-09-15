<?php

namespace App\Http\Controllers\API\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\PHC;
use App\Models\User;
use App\Notifications\PhcValidationRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingPhcApiController extends Controller
{
    //
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,pn_number',
            'handover_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'target_finish_date' => 'nullable|date',
            'client_pic_name' => 'required|string',
            'client_mobile' => 'nullable|string',
            'client_reps_office_address' => 'nullable|string',
            'client_site_representatives' => 'nullable|string',
            'client_site_address' => 'nullable|string',
            'site_phone_number' => 'nullable|string',
            'pic_marketing_id' => 'nullable|exists:users,id',
            'pic_engineering_id' => 'nullable|exists:users,id',
            'ho_marketings_id' => 'nullable|exists:users,id',
            'ho_engineering_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'retention' => 'nullable|string',
            'warranty' => 'nullable|string',
            'penalty' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';

        // 🔹 Buat PHC
        $phc = PHC::create($validated);

        // 🔹 Update tanggal PHC di project terkait
        $phc->project()->update([
            'phc_dates' => now(),
        ]);

        $approverIds = [];

        /**
         * 1. Tambah approval HO Marketing & PIC Marketing
         */
        foreach (array_filter([$request->ho_marketings_id, $request->pic_marketing_id]) as $userId) {
            Approval::create([
                'approvable_type' => PHC::class,
                'type' => 'PHC',
                'approvable_id'   => $phc->id,
                'user_id'         => $userId,
                'status'          => 'pending',
            ]);
            $approverIds[] = $userId;
        }

        /**
         * 2. HO Engineering kosong → assign semua role validator
         */
        if (empty($request->ho_engineering_id)) {
            $validatorUsers = User::whereHas('role', function ($q) {
                $q->whereIn('name', ['project manager', 'project controller', 'super_admin']);
            })->get();

            foreach ($validatorUsers as $user) {
                Approval::create([
                    'approvable_type' => PHC::class,
                    'type' => 'PHC',
                    'approvable_id'   => $phc->id,
                    'user_id'         => $user->id,
                    'status'          => 'pending',
                ]);
                $user->notify(new PhcValidationRequested($phc));
                $approverIds[] = $user->id;
            }
        } else {
            // Kalau ada HO Engineering
            Approval::create([
                'approvable_type' => PHC::class,
                'type' => 'PHC',
                'approvable_id'   => $phc->id,
                'user_id'         => $request->ho_engineering_id,
                'status'          => 'pending',
            ]);

            User::find($request->ho_engineering_id)?->notify(new PhcValidationRequested($phc));
            $approverIds[] = $request->ho_engineering_id;
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'PHC created successfully, approvals assigned.',
            'data'    => [
                'phc'        => $phc,
                'approvers'  => $approverIds,
            ]
        ]);
    }
}
