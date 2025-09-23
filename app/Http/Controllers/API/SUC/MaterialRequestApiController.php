<?php

namespace App\Http\Controllers\API\SUC;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use App\Models\Project;
use Illuminate\Http\Request;

class MaterialRequestApiController extends Controller
{
    //
    /**
     * Display a listing of material requests.
     */
    public function index()
    {
        $materials = MaterialRequest::with(['project', 'creator', 'mrHandover'])
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
            'target_date'        => 'required|date',
            'material_handover'  => 'nullable|exists:users,id',
            'remark'             => 'nullable|string',
        ]);

        // ✅ Cari last material number berdasarkan project (pn_id)
        $lastMr = MaterialRequest::where('pn_id', $validated['pn_id'])
                    ->orderByDesc('material_number')
                    ->first();

        $nextNumber = $lastMr ? $lastMr->material_number + 1 : 1;

        // ✅ Buat record baru
        $material = MaterialRequest::create([
            'pn_id'               => $validated['pn_id'],
            'material_number'     => $nextNumber,                    // Auto nomor per PN
            'material_description'=> $validated['material_description'], // Input manual
            'material_created'    => now(),
            'created_by'          => $request->user()->id ?? null,      // user yang create
            'target_date'         => $validated['target_date'],
            'material_status'     => 'On Progress',                  // Default status
            'material_handover'   => $validated['material_handover'],
            'ho_date'             => now(),
            'additional_material' => 0,                              // Default 0
            'remark'              => $validated['remark'] ?? null,
        ]);

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
            'target_date' => 'sometimes|date',
            'cancel_date' => 'nullable|date',
            'complete_date' => 'nullable|date',
            'material_status' => 'sometimes|in:On Progress,Canceled,Hold,Delayed,Completed',
            'additional_material' => 'sometimes|boolean',
            'remark' => 'nullable|string',
        ]);

        $materialRequest->update($validated);

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
        if (in_array($materialRequest->material_status, ['Canceled', 'Completed'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This material request is already closed',
            ], 400);
        }

        $materialRequest->update([
            'material_status' => 'Canceled',
            'cancel_date'     => now(),
            'complete_date'   => now(), // ikut diisi juga
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Material request has been canceled',
            'data'    => $materialRequest
        ]);
    }

    public function complete(MaterialRequest $materialRequest)
    {
        if (in_array($materialRequest->material_status, ['Canceled', 'Completed'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This material request is already closed',
            ], 400);
        }

        $materialRequest->update([
            'material_status' => 'Completed',
            'complete_date'   => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Material request has been completed',
            'data'    => $materialRequest
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
            ->filter(fn($mr) => in_array($mr->material_status, ['Completed', 'Canceled']))
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


}
