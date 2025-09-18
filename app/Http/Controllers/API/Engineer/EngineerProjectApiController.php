<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

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
}
