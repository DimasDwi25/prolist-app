<?php

namespace App\Http\Controllers\API\Finance;

use App\Http\Controllers\Controller;
use App\Models\InvoiceType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvoiceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $invoiceTypes = InvoiceType::all();
        return response()->json($invoiceTypes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'code_type' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $invoiceType = InvoiceType::create($request->all());
        return response()->json($invoiceType, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $invoiceType = InvoiceType::findOrFail($id);
        return response()->json($invoiceType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $invoiceType = InvoiceType::findOrFail($id);

        $request->validate([
            'code_type' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:255',
        ]);

        $invoiceType->update($request->all());
        return response()->json($invoiceType);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $invoiceType = InvoiceType::findOrFail($id);
        $invoiceType->delete();
        return response()->json(['message' => 'InvoiceType deleted successfully']);
    }
}
