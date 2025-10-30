<?php

namespace App\Http\Controllers\API\Finance;

use App\Http\Controllers\Controller;
use App\Models\HoldingTax;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HoldingTaxController extends Controller
{
    /**
     * Get holding tax by invoice_id.
     */
    public function getByInvoiceId(string $invoiceId): JsonResponse
    {
        $holdingTax = HoldingTax::with(['invoice.project', 'invoice.invoiceType'])
            ->where('invoice_id', $invoiceId)
            ->first();

        if (!$holdingTax) {
            return response()->json([
                'error' => 'Holding tax not found for this invoice'
            ], 404);
        }

        $clientName = null;
        if ($holdingTax->invoice?->project) {
            $clientName = $holdingTax->invoice->project->client?->name ?? $holdingTax->invoice->project->quotation?->client?->name;
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'holding_tax' => $holdingTax,
                'invoice' => $holdingTax->invoice,
                'client_name' => $clientName,
            ],
        ]);
    }

    /**
     * Update the specified holding tax.
     */
    public function update(Request $request, string $invoiceId): JsonResponse
    {
        $holdingTax = HoldingTax::where('invoice_id', $invoiceId)->first();

        if (!$holdingTax) {
            return response()->json([
                'error' => 'Holding tax not found for this invoice'
            ], 404);
        }

        $request->validate([
            'pph23_rate' => 'nullable|numeric|min:0|max:1',
            'nilai_pph23' => 'nullable|numeric|min:0',
            'pph42_rate' => 'nullable|numeric|min:0|max:1',
            'nilai_pph42' => 'nullable|numeric|min:0',
            'no_bukti_potong' => 'nullable|string|max:255',
            'nilai_potongan' => 'nullable|numeric|min:0',
            'tanggal_wht' => 'nullable|date',
        ]);

        // Calculate nilai_potongan if not provided
        $nilaiPotongan = $request->nilai_potongan ?? (($request->nilai_pph23 ?? $holdingTax->nilai_pph23 ?? 0) + ($request->nilai_pph42 ?? $holdingTax->nilai_pph42 ?? 0));

        $holdingTax->update([
            'pph23_rate' => $request->pph23_rate,
            'nilai_pph23' => $request->nilai_pph23,
            'pph42_rate' => $request->pph42_rate,
            'nilai_pph42' => $request->nilai_pph42,
            'no_bukti_potong' => $request->no_bukti_potong,
            'nilai_potongan' => $nilaiPotongan,
            'tanggal_wht' => $request->tanggal_wht,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Holding tax updated successfully',
            'data' => $holdingTax->load(['invoice.project', 'invoice.invoiceType']),
        ]);
    }
}
