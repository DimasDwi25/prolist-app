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
            'client_pic_name' => 'nullable|string',
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
            'retention' => 'nullable',
            'warranty' => 'nullable',
            'penalty' => 'nullable',
        ]);

        $mapRadio = function ($value) {
            return $value === 'A' ? 1 : 0;
        };

        $validated['costing_by_marketing'] = $mapRadio($request->costing_by_marketing);
        $validated['boq'] = $mapRadio($request->boq);
        $validated['retention'] = $request->retention;
        $validated['warranty'] = $request->warranty;
        $validated['penalty'] = $request->penalty;

        $validated['created_by'] = Auth::id();
        // $validated['status'] = 'pending';
        $validated['status'] = 'ready';

        // 🔹 Buat PHC
        $phc = PHC::create($validated);

        // 🔹 Update tanggal PHC di project terkait
        $phc->project()->update([
            'phc_dates' => now(),
        ]);

        // $approverIds = [];

        // $hasMarketing = !empty($request->ho_marketings_id) || !empty($request->pic_marketing_id);
        // $hasEngineering = !empty($request->ho_engineering_id);

        // if ($hasMarketing || $hasEngineering) {
        //     // 🔹 HO & PIC Marketing
        //     foreach (array_filter([$request->ho_marketings_id, $request->pic_marketing_id]) as $userId) {
        //         Approval::create([
        //             'approvable_type' => PHC::class,
        //             'type' => 'PHC',
        //             'approvable_id' => $phc->id,
        //             'user_id' => $userId,
        //             'status' => 'pending',
        //         ]);
        //         $approverIds[] = $userId;
        //     }

        //     // 🔹 HO Engineering
        //     if ($hasEngineering) {
        //         Approval::create([
        //             'approvable_type' => PHC::class,
        //             'type' => 'PHC',
        //             'approvable_id' => $phc->id,
        //             'user_id' => $request->ho_engineering_id,
        //             'status' => 'pending',
        //         ]);
        //         User::find($request->ho_engineering_id)?->notify(new PhcValidationRequested($phc));
        //         $approverIds[] = $request->ho_engineering_id;
        //     } elseif ($hasMarketing && !$hasEngineering) {
        //         // 🔹 Jika marketing ada tapi engineering kosong → kirim ke semua role engineering
        //         $engineeringUsers = User::whereHas('role', function($q){
        //             $q->whereIn('name', ['project manager', 'project controller', 'engineering_director']);
        //         })->get();

        //         foreach ($engineeringUsers as $user) {
        //             Approval::create([
        //                 'approvable_type' => PHC::class,
        //                 'type' => 'PHC',
        //                 'approvable_id' => $phc->id,
        //                 'user_id' => $user->id,
        //                 'status' => 'pending',
        //             ]);
        //             $user->notify(new PhcValidationRequested($phc));
        //             $approverIds[] = $user->id;
        //         }
        //     }
        // }

        return response()->json([
            'status'  => 'success',
            'message' => 'PHC created successfully, approvals assigned.',
            'data'    => [
                'phc'        => $phc,
                // 'approvers'  => $approverIds,
            ]
        ]);
    }

    public function show($id)
    {
        $phc = PHC::with([
            'project.quotation',
            'hoMarketing',
            'hoEngineering',
            'picMarketing',
            'picEngineering',
            'approvals'
        ])->find($id);

        if (!$phc) {
            return response()->json([
                'success' => false,
                'message' => 'PHC tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $phc
        ]);
    }

    public function update(Request $request, $id)
    {
        $phc = PHC::find($id);

        if (!$phc) {
            return response()->json([
                'success' => false,
                'message' => 'PHC tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'handover_date' => 'required|date',
            'start_date' => 'required|date',
            'target_finish_date' => 'required|date',
            'client_pic_name' => 'nullable|string',
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
            'boq' => 'nullable|string',
            'costing_by_marketing' => 'nullable|string',
        ]);

        // Mapping radio
        $mapRadio = fn($v) => $v === 'A' ? 1 : 0;
        foreach (['boq','costing_by_marketing'] as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = $mapRadio($validated[$field]);
            }
        }

         $phc->update($validated);


        // $newApprovers = [];

        // $hasMarketing = !empty($validated['ho_marketings_id']) || !empty($validated['pic_marketing_id']);
        // $hasEngineering = !empty($validated['ho_engineering_id']);

        // if ($hasMarketing || $hasEngineering) {
        //     // HO & PIC Marketing
        //     foreach (['ho_marketings_id','pic_marketing_id'] as $field) {
        //         if (!empty($validated[$field]) && !$phc->approvals()->where('user_id', $validated[$field])->exists()) {
        //             Approval::create([
        //                 'approvable_type' => PHC::class,
        //                 'type' => 'PHC',
        //                 'approvable_id' => $phc->id,
        //                 'user_id' => $validated[$field],
        //                 'status' => 'pending',
        //             ]);
        //             $newApprovers[] = $validated[$field];
        //         }
        //     }

        //     // HO Engineering
        //     if ($hasEngineering && !$phc->approvals()->where('user_id', $validated['ho_engineering_id'])->exists()) {
        //         Approval::create([
        //             'approvable_type' => PHC::class,
        //             'type' => 'PHC',
        //             'approvable_id' => $phc->id,
        //             'user_id' => $validated['ho_engineering_id'],
        //             'status' => 'pending',
        //         ]);
        //         User::find($validated['ho_engineering_id'])?->notify(new PhcValidationRequested($phc));
        //         $newApprovers[] = $validated['ho_engineering_id'];
        //     } elseif ($hasMarketing && !$hasEngineering) {
        //         // Jika marketing ada tapi engineering kosong → kirim ke semua role engineering
        //         $engineeringUsers = User::whereHas('role', function($q){
        //             $q->whereIn('name', ['project manager', 'project controller', 'engineering_director']);
        //         })->get();

        //         foreach ($engineeringUsers as $user) {
        //             if (!$phc->approvals()->where('user_id', $user->id)->exists()) {
        //                 Approval::create([
        //                     'approvable_type' => PHC::class,
        //                     'type' => 'PHC',
        //                     'approvable_id' => $phc->id,
        //                     'user_id' => $user->id,
        //                     'status' => 'pending',
        //                 ]);
        //                 $user->notify(new PhcValidationRequested($phc));
        //                 $newApprovers[] = $user->id;
        //             }
        //         }
        //     }
        // }
       
        return response()->json([
            'success' => true,
            'message' => 'PHC berhasil diperbarui',
            'data' => $phc
        ]);
    }


}