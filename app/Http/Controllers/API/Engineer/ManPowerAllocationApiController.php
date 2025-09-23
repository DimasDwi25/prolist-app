<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\ManPowerAllocation;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManPowerAllocationApiController extends Controller
{
    //
     /**
     * Get all allocations for a project
     */
    public function index($pn_number)
    {
        // $pn_number harus numeric, karena field pn_number di DB adalah unsignedBigInteger
        if (!is_numeric($pn_number)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid project number format. Expected numeric.',
            ], 400);
        }

        $project = Project::where('pn_number', $pn_number)->firstOrFail();

        $allocations = ManPowerAllocation::with(['user.role', 'project'])
            ->where('project_id', $project->pn_number)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $allocations,
        ]);
    }


    /**
     * Show single allocation
     */
    public function show($id)
    {
        $allocation = ManPowerAllocation::with(['user.role', 'project'])->find($id);

        if (!$allocation) {
            return response()->json([
                'success' => false,
                'message' => 'Allocation not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $allocation,
        ]);
    }

    /**
     * Store new allocation
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,pn_number',
            'user_id'    => 'required|exists:users,id',
            'role_id'    => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $allocation = ManPowerAllocation::create($request->only('project_id', 'user_id', 'role_id'));

        return response()->json([
            'success' => true,
            'data'    => $allocation->load(['user.role', 'project']),
        ], 201);
    }

    /**
     * Update allocation
     */
    public function update(Request $request, $id)
    {
        $allocation = ManPowerAllocation::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'project_id' => 'sometimes|exists:projects,pn_number',
            'user_id'    => 'sometimes|exists:users,id',
            'role_id'    => 'sometimes|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $allocation->update($request->only('project_id', 'user_id', 'role_id'));

        return response()->json([
            'success' => true,
            'data'    => $allocation->load(['user.role', 'project']),
        ]);
    }

    /**
     * Delete allocation
     */
    public function destroy($id)
    {
        $allocation = ManPowerAllocation::findOrFail($id);
        $allocation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Allocation deleted successfully',
        ]);
    }
}
