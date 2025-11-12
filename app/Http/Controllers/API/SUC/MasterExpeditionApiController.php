<?php

namespace App\Http\Controllers\API\SUC;

use App\Http\Controllers\Controller;
use App\Models\MasterExpedition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MasterExpeditionApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $masterExpeditions = MasterExpedition::all();
        return response()->json($masterExpeditions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alias_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'remark' => 'nullable|string',
        ]);

        $masterExpedition = MasterExpedition::create($request->all());
        return response()->json($masterExpedition, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $masterExpedition = MasterExpedition::findOrFail($id);
        return response()->json($masterExpedition);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $masterExpedition = MasterExpedition::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'alias_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'remark' => 'nullable|string',
        ]);

        $masterExpedition->update($request->all());
        return response()->json($masterExpedition);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $masterExpedition = MasterExpedition::findOrFail($id);
        $masterExpedition->delete();
        return response()->json(['message' => 'MasterExpedition deleted successfully']);
    }
}
