<?php

namespace App\Http\Controllers\API\Finance;

use App\Http\Controllers\Controller;
use App\Models\Retention;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RetentionController extends Controller
{
    /**
     * Display a listing of retentions.
     */
    public function index(Request $request): JsonResponse
    {
        $projectId = $request->query('project_id');
        $query = Retention::with(['invoice', 'project']);

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $retentions = $query->get();

        return response()->json($retentions);
    }

    /**
     * Display the specified retention.
     */
    public function show(string $id): JsonResponse
    {
        $retention = Retention::with(['invoice', 'project'])->findOrFail($id);
        return response()->json($retention);
    }

    /**
     * Update the specified retention.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $retention = Retention::findOrFail($id);

        $request->validate([
            'project_id' => 'sometimes|required|string',
            'retention_due_date' => 'nullable|date',
            'retention_value' => 'nullable|numeric',
            'invoice_id' => 'nullable|string',
        ]);

        $retention->update($request->all());

        return response()->json($retention);
    }

    /**
     * Remove the specified retention.
     */
    public function destroy(string $id): JsonResponse
    {
        $retention = Retention::findOrFail($id);
        $retention->delete();

        return response()->json(['message' => 'Retention deleted successfully']);
    }
}
