<?php

namespace App\Http\Controllers\API\Finance;

use App\Http\Controllers\Controller;
use App\Models\RequestInvoice;
use App\Models\RequestInvoiceDocument;
use App\Models\Project;
use App\Models\PHC;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RequestInvoiceApiController extends Controller
{
    /**
     * GET Request Invoices by Project PN Number
     */
    public function index($pn_number)
    {
        $project = Project::where('pn_number', $pn_number)->firstOrFail();

        $requestInvoices = RequestInvoice::with(['documents.documentPreparation', 'requestedBy', 'approvedBy', 'project'])
            ->where('project_id', $project->pn_number)
            ->orderBy('request_number')
            ->get();

        // Filter documents to only show where documentPreparation.is_applicable is true (value 1)
        $requestInvoices->each(function ($requestInvoice) {
            $requestInvoice->documents = $requestInvoice->documents->filter(function ($document) {
                return $document->documentPreparation && $document->documentPreparation->is_applicable == true;
            });
        });

        return response()->json([
            'success' => true,
            'data' => $requestInvoices,
        ]);
    }

    /**
     * CREATE new Request Invoice
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,pn_number',
            'description' => 'nullable|string',
            'documents' => 'array',
            'documents.*.document_preparation_id' => 'required|exists:document_preparations,id',
            'documents.*.notes' => 'nullable|string',
        ]);

        // cari nomor terakhir di project yang sama
        $lastRequest = RequestInvoice::where('project_id', $request->project_id)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastRequest) {
            $parts = explode('/', $lastRequest->request_number);
            if (count($parts) > 1) {
                $num = (int) $parts[1];
                $nextNumber = $num + 1;
            }
        }

        $requestNumber = $data['project_id'] . '/' . sprintf("%03d", $nextNumber);

        DB::transaction(function () use ($data, $requestNumber, $request) {
            $requestInvoice = RequestInvoice::create([
                'request_number' => $requestNumber,
                'project_id' => $data['project_id'],
                'requested_by' => $request->user()->id,
                'description' => $data['description'] ?? null,
                'status' => 'pending',
            ]);

            // Documents
            if (!empty($data['documents'])) {
                $documents = array_map(function ($doc) {
                    return [
                        'document_preparation_id' => $doc['document_preparation_id'],
                        'notes' => $doc['notes'] ?? null,
                    ];
                }, $data['documents']);
                $requestInvoice->documents()->createMany($documents);
            }

            // Fire event for notifications
            $userIds = User::whereHas('role', function ($query) {
                $query->whereIn('name', ['acc_fin_manager', 'acc_fin_supervisor', 'finance_administration']);
            })->pluck('id')->toArray();
            event(new \App\Events\RequestInvoiceCreated($requestInvoice, $userIds));
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Request Invoice created successfully',
        ], 201);
    }

    /**
     * UPDATE Request Invoice
     */
    public function update(Request $request, $id)
    {
        $requestInvoice = RequestInvoice::findOrFail($id);

        $data = $request->validate([
            'description' => 'nullable|string',
            'documents' => 'array',
            'documents.*.document_preparation_id' => 'required|exists:document_preparations,id',
            'documents.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($requestInvoice, $data) {
            $requestInvoice->update([
                'description' => $data['description'] ?? $requestInvoice->description,
            ]);

            // Update Documents
            if (isset($data['documents'])) {
                $requestInvoice->documents()->delete();
                $documents = array_map(function ($doc) {
                    return [
                        'document_preparation_id' => $doc['document_preparation_id'],
                        'notes' => $doc['notes'] ?? null,
                    ];
                }, $data['documents']);
                $requestInvoice->documents()->createMany($documents);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Request Invoice updated successfully',
        ]);
    }

    /**
     * SHOW single Request Invoice
     */
    public function show($id)
    {
        $requestInvoice = RequestInvoice::with(['documents.documentPreparation', 'requestedBy', 'approvedBy', 'project'])
            ->find($id);

        if (!$requestInvoice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request Invoice not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $requestInvoice,
        ]);
    }

    /**
     * GET Request Invoice Summary with filtering like InvoiceController
     */
    public function summary(Request $request)
    {
        // 🔹 Ambil daftar tahun dari project (pn_number)
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

        // 🔹 Query projects (similar to InvoiceController - show all projects with request invoice counts)
        $projects = Project::query()
            ->with(['client', 'quotation.client', 'requestInvoices'])
            ->orderByRaw('CAST(LEFT(CAST(pn_number AS VARCHAR), 2) AS INT) DESC') // ambil 2 digit pertama sebagai tahun
            ->orderByRaw('CAST(SUBSTRING(CAST(pn_number AS VARCHAR), 3, LEN(CAST(pn_number AS VARCHAR)) - 2) AS INT) DESC'); // ambil nomor urut

        // 🔹 Filter berdasarkan range
        switch ($rangeType) {
            case 'monthly':
                $projects->whereYearFromPn($year);
                if ($month) {
                    // For monthly, we need to filter by month from pn_number or created_at
                    // Since pn_number doesn't have month, we'll use created_at as fallback
                    $projects->whereMonth('po_date', $month);
                }
                break;

            case 'weekly':
                $projects->whereBetween('po_date', [
                    now()->startOfWeek(), now()->endOfWeek()
                ]);
                break;

            case 'custom':
                if ($from && $to) {
                    $projects->whereBetween('po_date', [$from, $to]);
                }
                break;

            default:
                $projects->whereYearFromPn($year);
                break;
        }

        $projects = $projects->get()
            ->map(function ($project) {
                $totalRequestInvoices = $project->requestInvoices->count();

                // Get client name from project->client or project->quotation->client
                $clientName = $project->client ? $project->client->name :
                    ($project->quotation && $project->quotation->client ? $project->quotation->client->name : null);

                return [
                    'pn_number' => $project->pn_number,
                    'project_name' => $project->project_name,
                    'client_name' => $clientName,
                    'total_request_invoices' => $totalRequestInvoices,
                ];
            });

        return response()->json([
            'status' => 'success',
            'availableYears' => $availableYears,
            'year' => $year,
            'range_type' => $rangeType,
            'month' => $month,
            'from_date' => $from,
            'to_date' => $to,
            'data' => $projects,
        ]);
    }

    /**
     * Ambil daftar tahun yang ada di pn_number (unik, ascending)
     */
    private function getAvailableYears(): array
    {
        return Project::selectRaw('LEFT(pn_number, 2) as year_short')
            ->distinct()
            ->pluck('year_short')
            ->map(fn($y) => 2000 + (int)$y)
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * GET PHC Documents for a Project
     */
    public function getPhcDocuments($pn_number)
    {
        $project = Project::where('pn_number', $pn_number)->firstOrFail();

        $phc = PHC::where('project_id', $pn_number)->first();

        if (!$phc) {
            return response()->json([
                'success' => false,
                'message' => 'PHC not found for this project',
            ], 404);
        }

        $phcDocuments = $phc->documentPreparations()
            ->with('document')
            ->where('is_applicable', true)
            ->get()
            ->map(function ($preparation) {
                return [
                    'id' => $preparation->id,
                    'document_name' => $preparation->document->name ?? 'Unknown Document',
                    'is_applicable' => $preparation->is_applicable,
                    'date_prepared' => $preparation->date_prepared,
                    'attachment_path' => $preparation->attachment_path,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $phcDocuments,
        ]);
    }


}
