<?php

namespace App\Http\Controllers\API\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as FacadesLog;

class MarketingProjectController extends Controller
{
    //
    public function index()
    {
        $projects = Project::with(['category', 'quotation.client', 'client', 'statusProject'])
        ->orderByRaw('CAST(LEFT(CAST(pn_number AS VARCHAR), 2) AS INT) DESC') // ambil 2 digit pertama sebagai tahun
        ->orderByRaw('CAST(SUBSTRING(CAST(pn_number AS VARCHAR), 3, LEN(CAST(pn_number AS VARCHAR)) - 2) AS INT) DESC') // ambil nomor urut
        ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Projects fetched successfully',
            'data'    => $projects,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'project_name'           => 'required|string|max:255',
                'categories_project_id'  => 'nullable|exists:project_categories,id',
                'quotations_id'          => 'required|exists:quotations,quotation_number',
                'phc_dates'              => 'nullable|date',
                'mandays_engineer'       => 'nullable|integer',
                'mandays_technician'     => 'nullable|integer',
                'target_dates'           => 'nullable|date',
                'status_project_id'      => 'nullable|exists:status_projects,id',
                'po_date'                => 'nullable|date',
                'sales_weeks'            => 'nullable|string|max:50',
                'po_number'              => 'nullable|string|max:100',
                'po_value'               => 'nullable|numeric',
                'is_confirmation_order'  => 'nullable|boolean',
                'parent_pn_number'       => 'nullable|exists:projects,pn_number',
                'client_id'              => 'nullable|exists:clients,id',
            ]);

            // Set default false jika checkbox tidak dicentang
            $validated['is_confirmation_order'] = $request->boolean('is_confirmation_order', false);

             // ✅ default category = 1 kalau tidak ada input
            $validated['status_project_id'] = $request->input('status_project_id', 1);

            $project = Project::create($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Project created successfully',
                'data'    => $project,
            ]);
        } catch (\Throwable $e) {
            FacadesLog::error($e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create project',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Project $project)
    {
        $project->load([
            'variants.statusProject',
            'parent.statusProject',
            'statusProject',
            'category',
            'phc.approvals.user',
            'client',
            'quotation'
        ]);

        $pendingApprovals = $project->phc
            ? $project->phc->approvals()->where('status', 'pending')->with('user')->get()
            : collect();

        return response()->json([
            'status'  => 'success',
            'message' => 'Project detail fetched successfully',
            'data'    => [
                'project'          => $project,
                'pendingApprovals' => $pendingApprovals,
            ],
        ]);
    }

    public function update(Request $request, Project $project)
    {
        try {
            $validated = $request->validate([
                'project_name'           => 'sometimes|string|max:255',
                'categories_project_id'  => 'sometimes|exists:project_categories,id',
                'quotations_id'          => 'sometimes|exists:quotations,quotation_number',
                'phc_dates'              => 'sometimes|date',
                'mandays_engineer'       => 'sometimes|integer',
                'mandays_technician'     => 'sometimes|integer',
                'target_dates'           => 'sometimes|date',
                'material_status'        => 'sometimes|string',
                'dokumen_finish_date'    => 'sometimes|date',
                'engineering_finish_date'=> 'sometimes|date',
                'jumlah_invoice'         => 'sometimes|integer',
                'status_project_id'      => 'sometimes|exists:status_projects,id',
                'project_progress'       => 'sometimes|integer|min:0|max:100',
                'po_date'                => 'sometimes|date',
                'sales_weeks'            => 'sometimes|string|max:50',
                'po_number'              => 'sometimes|string|max:100',
                'po_value'               => 'sometimes|numeric',
                'is_confirmation_order'  => 'sometimes|boolean',
                'parent_pn_number'       => 'sometimes|exists:projects,pn_number',
                'client_id'              => 'sometimes|exists:clients,id',
            ]);

            $validated['is_confirmation_order'] = $request->has('is_confirmation_order');

            $project->update($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Project updated successfully',
                'data'    => $project,
            ]);
        } catch (\Throwable $e) {
            FacadesLog::error($e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update project',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Project $project)
    {
        try {
            \App\Models\PhcApproval::whereIn('phc_id', $project->phcs()->pluck('id'))->delete();
            $project->phcs()->delete();
            $project->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Project deleted successfully',
            ]);
        } catch (\Throwable $e) {
            FacadesLog::error($e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to delete project',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function generateNumber(Request $request)
    {
        $isCO = $request->boolean('is_co', false);
        $currentProject = $request->input('current_project');
        $simple = $request->boolean('simple', false);

        // kalau sudah ada project number dan bukan CO, return existing
        if ($currentProject && !$isCO) {
            $data = [
                'project_number' => $currentProject,
            ];
            return $simple
                ? response()->json($data)
                : response()->json([
                    'status'  => 'success',
                    'message' => 'Keep existing project number',
                    'data'    => $data,
                ]);
        }

        // generate baru
        $pnNumber = Project::generatePnNumber();
        $projectNumber = Project::generateProjectNumber($pnNumber, $isCO);

        $data = [
            'pn_number' => $pnNumber,
            'project_number' => $projectNumber,
        ];

        return $simple
            ? response()->json($data)
            : response()->json([
                'status'  => 'success',
                'message' => 'Generated project number',
                'data'    => $data,
            ]);
    }

}
