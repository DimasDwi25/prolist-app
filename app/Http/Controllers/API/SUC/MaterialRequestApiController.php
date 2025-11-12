<?php

namespace App\Http\Controllers\API\SUC;

use App\Http\Controllers\Controller;
use App\Models\MasterStatusMr;
use App\Models\MaterialRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MaterialRequestApiController extends Controller
{
    //
    /**
     * Display a listing of material requests.
     */
    public function index()
    {
        $materials = MaterialRequest::with(['project', 'creator', 'mrHandover', 'materialStatus'])
            ->orderBy('material_number', 'asc') // ascending
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $materials
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'pn_id'              => 'required|exists:projects,pn_number',
            'material_description' => 'required|string',     // Judul MR / deskripsi
            'target_date'        => 'nullable|date',
            'material_handover'  => 'nullable|exists:users,id',
            'remark'             => 'nullable|string',
        ]);

        // ✅ Cari last material number berdasarkan project (pn_id)
        $lastMr = MaterialRequest::where('pn_id', $validated['pn_id'])
                    ->orderByDesc('material_number')
                    ->first();

        $nextNumber = $lastMr ? $lastMr->material_number + 1 : 1;

        // ✅ Cari status 'On Progress'
        $onProgressStatus = MasterStatusMr::where('name', 'On Progress')->first();
        if (!$onProgressStatus) {
            return response()->json([
                'status' => 'error',
                'message' => 'Default status "On Progress" not found in master_status_mrs table'
            ], 500);
        }

        // ✅ Buat record baru
        $material = MaterialRequest::create([
            'pn_id'               => $validated['pn_id'],
            'material_number'     => $nextNumber,                    // Auto nomor per PN
            'material_description'=> $validated['material_description'], // Input manual
            'material_created'    => now(),
            'created_by'          => $request->user()->id ?? null,      // user yang create
            'target_date'         => $validated['target_date'],
            'material_status_id'  => $onProgressStatus->id,          // Default status ID
            'material_handover'   => $validated['material_handover'],
            'ho_date'             => $validated['material_handover'] ? now() : null,
            'additional_material' => 0,                              // Default 0
            'remark'              => $validated['remark'] ?? null,
        ]);

        // ✅ Jika material_handover diisi, otomatis handover
        if ($validated['material_handover']) {
            $this->performHandover($material, $validated['material_handover']);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $material
        ], 201);
    }


    /**
     * Display the specified material request.
     */
    public function show(MaterialRequest $materialRequest)
    {
        return response()->json([
            'status' => 'success',
            'data' => $materialRequest->load(['project', 'creator'])
        ]);
    }

    /**
     * Update the specified material request.
     */
    public function update(Request $request, MaterialRequest $materialRequest)
    {
        $validated = $request->validate([
            'pn_id' => 'nullable|exists:projects,pn_number',
            'material_number' => 'sometimes|integer',
            'material_description' => 'sometimes|string',
            'created_by' => 'sometimes|exists:users,id',
            'material_handover' => 'sometimes|exists:users,id',
            'target_date' => 'sometimes|nullable|date',
            'cancel_date' => 'nullable|date',
            'complete_date' => 'nullable|date',
            'material_status_id' => 'sometimes|exists:master_status_mrs,id',
            'additional_material' => 'sometimes|boolean',
            'remark' => 'nullable|string',
        ]);

        // Prevent changing status to 'Canceled' or 'Completed'
        if (isset($validated['material_status_id'])) {
            $status = MasterStatusMr::find($validated['material_status_id']);
            if ($status && in_array($status->name, ['Canceled', 'Completed'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot change status to ' . $status->name . '. Use the specific cancel or complete methods instead.'
                ], 400);
            }
        }

        // Track if material_handover was changed
        $handoverChanged = isset($validated['material_handover']) &&
                          $validated['material_handover'] != $materialRequest->material_handover;

        $materialRequest->update($validated);

        // ✅ Jika material_handover diubah dan diisi, otomatis handover
        if ($handoverChanged && $validated['material_handover']) {
            $this->performHandover($materialRequest, $validated['material_handover']);
        }

        return response()->json([
            'status' => 'success',
            'data' => $materialRequest
        ]);
    }

    /**
     * Remove the specified material request.
     */
    public function destroy(MaterialRequest $materialRequest)
    {
        $materialRequest->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Material request deleted successfully'
        ]);
    }

    public function cancel(MaterialRequest $materialRequest)
    {
        // Check if already closed using relation
        if ($materialRequest->materialStatus && in_array($materialRequest->materialStatus->name, ['Canceled', 'Completed'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This material request is already closed',
            ], 400);
        }

        // Find Canceled status
        $canceledStatus = MasterStatusMr::where('name', 'Canceled')->first();
        if (!$canceledStatus) {
            return response()->json([
                'status' => 'error',
                'message' => 'Status "Canceled" not found in master_status_mrs table'
            ], 500);
        }

        $materialRequest->update([
            'material_status_id' => $canceledStatus->id,
            'cancel_date'        => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Material request has been canceled',
            'data'    => $materialRequest
        ]);
    }

    public function handover(Request $request, MaterialRequest $materialRequest)
    {
        $validated = $request->validate([
            'material_handover' => 'required|exists:users,id',
            'pin' => 'required|string',
        ]);

        // Check if already closed using relation
        if ($materialRequest->materialStatus && in_array($materialRequest->materialStatus->name, ['Canceled', 'Completed'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This material request is already closed',
            ], 400);
        }

        // Get the handover user
        $handoverUser = \App\Models\User::find($validated['material_handover']);
        if (!$handoverUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'Handover user not found',
            ], 404);
        }

        // Validate PIN against handover user's PIN first
        if (!Hash::check($validated['pin'], $handoverUser->pin)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid PIN for the selected handover user',
            ], 400);
        }

        // Perform handover only if PIN is valid
        $this->performHandover($materialRequest, $validated['material_handover']);

        // Find Completed status
        $completedStatus = MasterStatusMr::where('name', 'Completed')->first();
        if (!$completedStatus) {
            return response()->json([
                'status' => 'error',
                'message' => 'Status "Completed" not found in master_status_mrs table'
            ], 500);
        }

        // Immediately approve and complete
        $materialRequest->update([
            'material_status_id' => $completedStatus->id,
            'complete_date'      => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Material request has been handed over and approved',
            'data'    => $materialRequest
        ]);
    }



    private function performHandover(MaterialRequest $materialRequest, $handoverUserId)
    {
        // Find Waiting Approval status
        $waitingApprovalStatus = MasterStatusMr::where('name', 'Waiting Approval')->first();
        if (!$waitingApprovalStatus) {
            throw new \Exception('Status "Waiting Approval" not found in master_status_mrs table');
        }

        $materialRequest->update([
            'material_handover'  => $handoverUserId,
            'material_status_id' => $waitingApprovalStatus->id,
            'ho_date'            => now(),
        ]);
    }

    public function getMrSummary()
    {
        $projects = Project::with(['materialRequests', 'statusProject'])
        ->orderByRaw('CAST(LEFT(CAST(pn_number AS VARCHAR), 2) AS INT) DESC')
        ->orderByRaw('CAST(SUBSTRING(CAST(pn_number AS VARCHAR), 3, LEN(CAST(pn_number AS VARCHAR)) - 2) AS INT) DESC')
        ->get();

        $summary = $projects->map(function ($project) {
            $totalMr = $project->materialRequests->count();
            $completedMr = $project->materialRequests
            ->filter(fn($mr) => $mr->materialStatus && in_array($mr->materialStatus->name, ['Completed', 'Canceled']))
            ->count();


            $mrProgress = $totalMr > 0
                ? round(($completedMr / $totalMr) * 100, 2)
                : 0;

            return [
                'pn_number'     => $project->pn_number,
                'project_number' => $project->project_number,
                'project_name'  => $project->project_name,
                'total_mr'      => $totalMr,
                'completed_mr'  => $completedMr,
                'mr_progress'   => $mrProgress,
                'status_project' => $project->statusProject ? [
                    'id'   => $project->statusProject->id,
                    'name' => $project->statusProject->name,
                ] : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $summary,
        ]);
    }

    public function getAvailableStatuses()
    {
        $statuses = MasterStatusMr::whereNotIn('name', ['Canceled', 'Completed'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $statuses
        ]);
    }


}
