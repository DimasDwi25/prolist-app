<?php

namespace App\Http\Controllers\API\Finance;

use App\Http\Controllers\Controller;
use App\Models\RequestInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RequestInvoiceListApiController extends Controller
{
    /**
     * Display a listing of request invoices with filtering by year and range.
     */
    public function index(Request $request)
    {
        // 🔹 Ambil daftar tahun dari request_invoices created_at
        $availableYears = $this->getAvailableYears();

        // 🔹 Tahun aktif
        $yearParam = $request->query('year');
        $year = $yearParam
            ? (int) $yearParam
            : (!empty($availableYears) ? end($availableYears) : now()->year);

        // 🔹 Filter & range
        $rangeType = $request->query('range_type', 'yearly');
        $month = $request->query('month');
        $from = $request->query('from_date');
        $to = $request->query('to_date');

        // 🔹 Query request invoices
        $requestInvoices = RequestInvoice::query()
            ->with([
                'documents' => function ($query) {
                    $query->whereHas('documentPreparation', function ($subQuery) {
                        $subQuery->where('is_applicable', true);
                    })->with(['documentPreparation' => function ($subQuery) {
                        $subQuery->select('id', 'document_id', 'is_applicable')
                            ->with(['document' => function ($subSubQuery) {
                                $subSubQuery->select('id', 'name');
                            }]);
                    }]);
                },
                'requestedBy' => function ($query) {
                    $query->select('id', 'name');
                },
                'approvedBy' => function ($query) {
                    $query->select('id', 'name');
                },
                'project' => function ($query) {
                    $query->select('pn_number', 'project_name')
                        ->selectRaw('(SELECT c.name FROM quotations q JOIN clients c ON q.client_id = c.id WHERE q.quotation_number = projects.quotations_id) as client_name');
                }
            ])
            ->orderBy('created_at', 'desc');

        // 🔹 Filter berdasarkan range
        switch ($rangeType) {
            case 'monthly':
                $requestInvoices->whereYear('created_at', $year);
                if ($month) {
                    $requestInvoices->whereMonth('created_at', $month);
                }
                break;

            case 'weekly':
                $requestInvoices->whereBetween('created_at', [
                    now()->startOfWeek(), now()->endOfWeek()
                ]);
                break;

            case 'custom':
                if ($from && $to) {
                    $requestInvoices->whereBetween('created_at', [$from, $to]);
                }
                break;

            default:
                $requestInvoices->whereYear('created_at', $year);
                break;
        }

        $requestInvoices = $requestInvoices->get();

        return response()->json([
            'status' => 'success',
            'availableYears' => $availableYears,
            'year' => $year,
            'range_type' => $rangeType,
            'month' => $month,
            'from_date' => $from,
            'to_date' => $to,
            'data' => $requestInvoices,
        ]);
    }

    /**
     * Display the specified request invoice.
     */
    public function show($id)
    {
        $requestInvoice = RequestInvoice::with([
            'documents' => function ($query) {
                $query->whereHas('documentPreparation', function ($subQuery) {
                    $subQuery->where('is_applicable', true);
                })->with(['documentPreparation.document']);
            },
            'requestedBy',
            'approvedBy',
            'project.client',
            'project.quotation.client'
        ])
            ->find($id);

        if (!$requestInvoice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request invoice not found',
            ], 404);
        }

        // Filter documents to only show where documentPreparation.is_applicable is true
        $requestInvoice->documents = $requestInvoice->documents->filter(function ($document) {
            return $document->documentPreparation && $document->documentPreparation->is_applicable == true;
        });

        return response()->json([
            'status' => 'success',
            'data' => $requestInvoice,
        ]);
    }

    /**
     * Approve a request invoice by changing status from pending to approved.
     */
    public function approve($id, Request $request)
    {
        $request->validate([
            'pin' => 'required|string',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->pin, $user->pin)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid PIN',
            ], 400);
        }

        $requestInvoice = RequestInvoice::find($id);

        if (!$requestInvoice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request invoice not found',
            ], 404);
        }

        if ($requestInvoice->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Request invoice is not in pending status',
            ], 400);
        }

        $requestInvoice->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Request invoice approved successfully',
            'data' => $requestInvoice,
        ]);
    }

    /**
     * Ambil daftar tahun yang ada di request_invoices created_at (unik, ascending)
     */
    private function getAvailableYears(): array
    {
        return RequestInvoice::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year')
            ->map(fn($y) => (int)$y)
            ->sort()
            ->values()
            ->toArray();
    }
}
