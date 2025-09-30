<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EngineerProjectApiController extends Controller
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

    public function engineerProjects()
    {
        $user = Auth::user();
        $userId = $user->id;

        if ($user->role->name !== 'engineer') {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $projects = Project::query()
            ->where(function ($q) use ($userId) {
                $q->whereHas('manPowerAllocations', function ($sub) use ($userId) {
                    $sub->where('user_id', $userId);
                })
                ->orWhereHas('phc', function ($sub) use ($userId) {
                    $sub->where('pic_engineering_id', $userId)
                        ->orWhere('ho_engineering_id', $userId);
                });
            })
            ->with(['client', 'quotation', 'phc', 'manPowerAllocations']) // optional eager load
            ->get();

        return response()->json([
            'data' => $projects,
        ]);
    }

    public function updateStatus(Request $request, $pn_number)
    {
        // Validasi request
        $validated = $request->validate([
            'status_project_id' => [
                'required',
                'integer',
            ],
        ]);

        // Ambil project berdasarkan pn_number
        $project = Project::where('pn_number', $pn_number)->first();

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        // Update status
        $project->status_project_id = $validated['status_project_id'];
        $project->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Project status updated successfully',
            'data' => $project,
        ]);
    }
}
