<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\PurposeWorkOrders;
use Illuminate\Http\Request;

class PurposeWorkOrderApiController extends Controller
{
    //
    // List semua purpose
    public function index()
    {
        $purposes = PurposeWorkOrders::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $purposes,
        ]);
    }

    // Show detail berdasarkan id
    public function show($id)
    {
        $purpose = PurposeWorkOrders::find($id);

        if (!$purpose) {
            return response()->json([
                'success' => false,
                'message' => 'Purpose not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $purpose,
        ]);
    }

    // Create new purpose
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $purpose = PurposeWorkOrders::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'data' => $purpose,
            'message' => 'Purpose created successfully',
        ], 201);
    }

    // Update purpose
    public function update(Request $request, $id)
    {
        $purpose = PurposeWorkOrders::find($id);

        if (!$purpose) {
            return response()->json([
                'success' => false,
                'message' => 'Purpose not found',
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $purpose->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'data' => $purpose,
            'message' => 'Purpose updated successfully',
        ]);
    }

    // Delete purpose
    public function destroy($id)
    {
        $purpose = PurposeWorkOrders::find($id);

        if (!$purpose) {
            return response()->json([
                'success' => false,
                'message' => 'Purpose not found',
            ], 404);
        }

        $purpose->delete();

        return response()->json([
            'success' => true,
            'message' => 'Purpose deleted successfully',
        ]);
    }
}
