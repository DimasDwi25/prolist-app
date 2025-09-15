<?php

namespace App\Http\Controllers\API\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class SalesReportApiController extends Controller
{
    //
    public function index()
    {
        $projects = Project::with(['category', 'quotation'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Sales report fetched successfully',
            'data'    => $projects,
        ]);
    }
}
