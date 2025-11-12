<?php

namespace App\Http\Controllers\API\SUC;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DestinationApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $destinations = Destination::all();
        return response()->json($destinations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'destination' => 'required|string|max:255',
            'address' => 'required|string',
            'alias' => 'required|string|max:255',
        ]);

        $destination = Destination::create($request->all());
        return response()->json($destination, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $destination = Destination::findOrFail($id);
        return response()->json($destination);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $destination = Destination::findOrFail($id);

        $request->validate([
            'destination' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string',
            'alias' => 'sometimes|required|string|max:255',
        ]);

        $destination->update($request->all());
        return response()->json($destination);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $destination = Destination::findOrFail($id);
        $destination->delete();
        return response()->json(['message' => 'Destination deleted successfully']);
    }
}
