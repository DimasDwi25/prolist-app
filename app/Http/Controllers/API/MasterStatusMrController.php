<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MasterStatusMr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MasterStatusMrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $masterStatusMr = MasterStatusMr::all();
        return response()->json($masterStatusMr);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $masterStatusMr = MasterStatusMr::create($request->all());
        return response()->json($masterStatusMr, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $masterStatusMr = MasterStatusMr::findOrFail($id);
        return response()->json($masterStatusMr);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $masterStatusMr = MasterStatusMr::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $masterStatusMr->update($request->all());
        return response()->json($masterStatusMr);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $masterStatusMr = MasterStatusMr::findOrFail($id);
        $masterStatusMr->delete();
        return response()->json(['message' => 'MasterStatusMr deleted successfully']);
    }
}
