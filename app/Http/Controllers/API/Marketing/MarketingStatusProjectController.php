<?php

namespace App\Http\Controllers\API\Marketing;

use App\Http\Controllers\Controller;
use App\Models\StatusProject;
use Illuminate\Http\Request;

class MarketingStatusProjectController extends Controller
{
    //
     // GET /api/status-projects
    public function index()
    {
        return response()->json(StatusProject::latest()->get());
    }

    // POST /api/status-projects
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:status_projects,name',
        ]);

        $status = StatusProject::create($validated);

        return response()->json([
            'message' => 'Status created successfully',
            'data' => $status
        ], 201);
    }

    // GET /api/status-projects/{id}
    public function show(StatusProject $statusProject)
    {
        return response()->json($statusProject);
    }

    // PUT/PATCH /api/status-projects/{id}
    public function update(Request $request, StatusProject $statusProject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:status_projects,name,' . $statusProject->id,
        ]);

        $statusProject->update($validated);

        return response()->json([
            'message' => 'Status updated successfully',
            'data' => $statusProject
        ]);
    }

    // DELETE /api/status-projects/{id}
    public function destroy(StatusProject $statusProject)
    {
        $statusProject->delete();

        return response()->json([
            'message' => 'Status deleted successfully'
        ]);
    }
}
