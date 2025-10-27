<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user']);

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->has('model_type') && $request->model_type) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by model id
        if ($request->has('model_id') && $request->model_id) {
            $query->where('model_id', $request->model_id);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->whereYear('created_at', $request->year);
        }

        // Filter by month (requires year)
        if ($request->has('month') && $request->month && $request->has('year') && $request->year) {
            $query->whereMonth('created_at', $request->month)
                  ->whereYear('created_at', $request->year);
        }

        // Filter by single date
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search in description
        if ($request->has('search') && $request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->orderBy('created_at', 'desc')
                      ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $logs,
            'message' => 'Activity logs retrieved successfully'
        ]);
    }

    public function show($id)
    {
        $log = ActivityLog::with(['user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $log,
            'message' => 'Activity log retrieved successfully'
        ]);
    }

    public function getActions()
    {
        $actions = ActivityLog::select('action')
                              ->distinct()
                              ->pluck('action')
                              ->toArray();

        return response()->json([
            'success' => true,
            'data' => $actions,
            'message' => 'Available actions retrieved successfully'
        ]);
    }

    public function getModelTypes()
    {
        $modelTypes = ActivityLog::select('model_type')
                                 ->distinct()
                                 ->whereNotNull('model_type')
                                 ->pluck('model_type')
                                 ->toArray();

        return response()->json([
            'success' => true,
            'data' => $modelTypes,
            'message' => 'Available model types retrieved successfully'
        ]);
    }
}
