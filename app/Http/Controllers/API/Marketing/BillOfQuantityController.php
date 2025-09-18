<?php

namespace App\Http\Controllers\API\Marketing;

use App\Http\Controllers\Controller;
use App\Models\BillOfQuantity;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BillOfQuantityController extends Controller
{
    //
    public function index($projectId)
    {
        $boqs = BillOfQuantity::where('project_id', $projectId)->get()->map(function($boq) {
            // pastikan portion dihitung jika belum ada
            if ($boq->material_portion === null && $boq->engineer_portion === null && $boq->project->value > 0) {
                $boq->material_portion = ($boq->material_value / $boq->project->value) * 100;
                $boq->engineer_portion = ($boq->engineer_value / $boq->project->value) * 100;
            }

            return [
                'id' => $boq->id,
                'project_id' => $boq->project_id,
                'item_number' => $boq->item_number,
                'description' => $boq->description,
                'material_value' => (float) $boq->material_value,
                'engineer_value' => (float) $boq->engineer_value,
                'material_portion' => (float) $boq->material_portion,
                'engineer_portion' => (float) $boq->engineer_portion,
                'progress_material' => (float) $boq->progress_material,
                'progress_engineer' => (float) $boq->progress_engineer,
                'total_progress' => (float) $boq->total_progress,
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $boqs,
        ]);
    }

    public function store(Request $request, $projectId)
    {
        $user = auth()->user();

        $allowedRoles = [
            'marketing',
            'marketing_admin',
            'manager_marketing',
            'sales_supervisor',
            'super_admin',
            'marketing_director',
            'supervisor marketing',
            'marketing_estimator',
        ];

        if (!$user->hasAnyRole($allowedRoles)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'description'     => 'required|string|max:255',
            'material_value'  => 'nullable|numeric|min:0',
            'engineer_value'  => 'nullable|numeric|min:0',
        ]);

        // ambil project
        $project = Project::findOrFail($projectId);

        // cari item_number terakhir di project ini
        $lastItem = BillOfQuantity::where('project_id', $project->pn_number)
            ->orderBy('item_number', 'desc')
            ->first();

        $nextItemNumber = $lastItem ? $lastItem->item_number + 1 : 1;

        // hitung portion otomatis
        $materialPortion = $project->po_value > 0
            ? min(($validated['material_value'] ?? 0) / $project->po_value * 100, 100)
            : 0;

        $engineerPortion = $project->po_value > 0
            ? min(($validated['engineer_value'] ?? 0) / $project->po_value * 100, 100)
            : 0;
        $materialValue = floatval($validated['material_value'] ?? 0);
        $engineerValue = floatval($validated['engineer_value'] ?? 0);

        // create record
        $boq = BillOfQuantity::create([
            'project_id'       => $project->pn_number,
            'item_number'      => $nextItemNumber,
            'description'      => $validated['description'],
            'material_value'   => $materialValue,
            'engineer_value'   => $engineerValue,
            'material_portion' => $materialPortion,
            'engineer_portion' => $engineerPortion,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'BOQ berhasil dibuat',
            'data'    => $boq,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $boq = BillOfQuantity::findOrFail($id);
        $project = $boq->project;

        $allowedRoles = [
            'marketing',
            'marketing_admin',
            'manager_marketing',
            'sales_supervisor',
            'super_admin',
            'marketing_director',
            'supervisor marketing',
            'marketing_estimator',
            'engineer',
            'engineering_director',
        ];

        $user = auth()->user();

        // cek apakah user memiliki salah satu role yang diizinkan
        if (!$user->hasAnyRole($allowedRoles)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // update progress untuk engineer
        if ($user->hasAnyRole(['engineer', 'engineering_director', 'super_admin'])) {
            $request->validate([
                'progress_material' => 'numeric|min:0|max:100',
                'progress_engineer' => 'numeric|min:0|max:100',
            ]);

            $boq->progress_material = $request->progress_material;
            $boq->progress_engineer = $request->progress_engineer;
            $boq->total_progress = ($boq->progress_material + $boq->progress_engineer) / 2;
            $boq->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Progress updated',
                'data' => $boq,
            ]);
        }

        // update BOQ untuk marketing
        if ($user->hasAnyRole([
            'marketing',
            'marketing_admin',
            'manager_marketing',
            'sales_supervisor',
            'super_admin',
            'marketing_director',
            'supervisor marketing',
            'marketing_estimator',
        ])) {
            $request->validate([
                'description' => 'sometimes|string',
                'material_value' => 'sometimes|numeric|min:0',
                'engineer_value' => 'sometimes|numeric|min:0',
            ]);

            $boq->description = $request->description;
            $boq->material_value = $request->material_value;
            $boq->engineer_value = $request->engineer_value;

            $boq->material_portion = $project->po_value > 0
                ? min(($request->material_value / $project->po_value) * 100, 100)
                : 0;
            $boq->engineer_portion = $project->po_value > 0
                ? min(($request->engineer_value / $project->po_value) * 100, 100)
                : 0;

            $boq->save();

            return response()->json([
                'status' => 'success',
                'message' => 'BOQ updated',
                'data' => $boq,
            ]);
        }

    }

}
