<?php

namespace App\Http\Controllers\API\SUC;

use App\Http\Controllers\Controller;
use App\Models\MasterTypePackingList;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MasterTypePackingListApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $masterTypePackingLists = MasterTypePackingList::all();
        return response()->json($masterTypePackingLists);
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

        $masterTypePackingList = MasterTypePackingList::create($request->all());
        return response()->json($masterTypePackingList, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $masterTypePackingList = MasterTypePackingList::findOrFail($id);
        return response()->json($masterTypePackingList);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $masterTypePackingList = MasterTypePackingList::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $masterTypePackingList->update($request->all());
        return response()->json($masterTypePackingList);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $masterTypePackingList = MasterTypePackingList::findOrFail($id);
        $masterTypePackingList->delete();
        return response()->json(['message' => 'MasterTypePackingList deleted successfully']);
    }
}
