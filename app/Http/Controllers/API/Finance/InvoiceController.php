<?php

namespace App\Http\Controllers\API\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvoiceType;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices by project_id with year filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $projectId = $request->query('project_id');
        if (!$projectId) {
            return response()->json(['error' => 'project_id is required'], 400);
        }

        $invoices = Invoice::where('project_id', $projectId)
            ->with(['project', 'invoiceType', 'payments'])
            ->orderBy('invoice_number_in_project', 'asc')
            ->get()
            ->map(function ($invoice) {
                $invoice->total_payment_amount = $invoice->payments->sum('payment_amount');
                return $invoice;
            });

        $totalPayment = $invoices->sum('total_payment_amount');
        $totalInvoiceValue = $invoices->sum('invoice_value');
        $outstandingPayment = $totalInvoiceValue - $totalPayment;

        return response()->json([
            'invoices' => $invoices,
            'total_payment' => $totalPayment,
            'outstanding_payment' => $outstandingPayment
        ]);
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => 'required|string',
            'invoice_type_id' => 'nullable|integer',
            'no_faktur' => 'nullable|string',
            'invoice_date' => 'nullable|date',
            'invoice_description' => 'nullable|string',
            'invoice_value' => 'nullable|numeric',
            'invoice_due_date' => 'nullable|date',
            'payment_status' => 'nullable|in:unpaid,partial,paid',
            'remarks' => 'nullable|string',
            'currency' => 'nullable|in:IDR,USD',
            'invoice_sequence' => 'nullable|integer|min:1',
        ]);

        // Fetch project to check value
        $project = Project::findOrFail($request->project_id);

        // Check if adding this invoice would exceed project value
        $currentTotal = Invoice::where('project_id', $request->project_id)->sum('invoice_value');
        if ($currentTotal + $request->invoice_value > $project->po_value) {
            return response()->json(['error' => 'Invoice total exceeds project value'], 400);
        }

        // Determine invoice sequence: custom or auto-generated
        if ($request->has('invoice_sequence')) {
            $invoiceNumberInProject = $request->invoice_sequence;
            // Check uniqueness within project
            if (Invoice::where('project_id', $request->project_id)->where('invoice_number_in_project', $invoiceNumberInProject)->exists()) {
                return response()->json(['error' => 'Invoice sequence already exists for this project'], 400);
            }
        } else {
            $invoiceNumberInProject = $this->getInvoiceNumberInProject($request->project_id);
        }

        // Generate invoice_id based on template IP25001
        $invoiceId = $this->generateInvoiceId($request->project_id, $request->invoice_type_id, $invoiceNumberInProject);

        $data = $request->all();
        $data['invoice_id'] = $invoiceId;
        $data['invoice_number_in_project'] = $invoiceNumberInProject;

        $invoice = Invoice::create($data);

        // Set default payment_status if not provided
        if (!isset($data['payment_status'])) {
            $invoice->update(['payment_status' => 'unpaid']);
        }

        return response()->json($invoice, 201);
    }

    /**
     * Display the specified invoice.
     */
    public function show(string $id): JsonResponse
    {
        $invoice = Invoice::with(['project', 'invoiceType', 'payments'])->findOrFail($id);
        $invoice->total_payment_amount = $invoice->payments->sum('payment_amount');
        return response()->json($invoice);
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $invoice = Invoice::findOrFail($id);

        $request->validate([
            'project_id' => 'sometimes|required|string',
            'invoice_type_id' => 'nullable|integer',
            'no_faktur' => 'nullable|string',
            'invoice_date' => 'nullable|date',
            'invoice_description' => 'nullable|string',
            'invoice_value' => 'nullable|numeric',
            'invoice_due_date' => 'nullable|date',
            'payment_status' => 'nullable|in:unpaid,partial,paid',
            'remarks' => 'nullable|string',
            'currency' => 'nullable|in:IDR,USD',
        ]);

        // Check if trying to change project_id and payments exist
        if ($request->has('project_id') && $request->project_id != $invoice->project_id && $invoice->payments()->exists()) {
            return response()->json(['error' => 'Cannot change project when payments exist'], 400);
        }

        // Determine the project_id to use for validation
        $projectIdForValidation = $request->project_id ?? $invoice->project_id;

        // Fetch the project for validation
        $projectForValidation = Project::findOrFail($projectIdForValidation);

        // Calculate current total for the project (excluding current invoice if updating)
        $currentTotal = Invoice::where('project_id', $projectIdForValidation)
            ->where('invoice_id', '!=', $id)
            ->sum('invoice_value');

        // Invoice value to check
        $invoiceValueToCheck = $request->invoice_value ?? $invoice->invoice_value;

        // Check if total would exceed project value
        if ($currentTotal + $invoiceValueToCheck > $projectForValidation->po_value) {
            return response()->json(['error' => 'Invoice total exceeds project value'], 400);
        }

        $data = $request->all();

        // Handle invoice_type_id change: regenerate invoice_id based on new type, keep sequence number
        if ($request->has('invoice_type_id') && $request->invoice_type_id != $invoice->invoice_type_id) {
            $oldInvoiceId = $invoice->invoice_id;
            $sequence = substr($oldInvoiceId, -3); // Extract last 3 characters as sequence (e.g., '001')

            $project = Project::findOrFail($invoice->project_id);
            $yearShort = substr((string)$project->pn_number, 0, 2); // e.g., '25'

            $newCodeType = '00'; // default
            $newInvoiceType = InvoiceType::find($request->invoice_type_id);
            if ($newInvoiceType) {
                $newCodeType = $newInvoiceType->code_type;
            }

            $newInvoiceId = $newCodeType . $yearShort . $sequence; // e.g., 'IP25001'

            DB::transaction(function () use ($oldInvoiceId, $newInvoiceId) {
                // Update all payments to reference the new invoice_id
                InvoicePayment::where('invoice_id', $oldInvoiceId)->update(['invoice_id' => $newInvoiceId]);
                // Update the invoice's invoice_id using raw SQL since it's the primary key
                DB::statement("UPDATE invoices SET invoice_id = ? WHERE invoice_id = ?", [$newInvoiceId, $oldInvoiceId]);
            });

            // Reload the invoice with the new ID
            $invoice = Invoice::find($newInvoiceId);
            // Remove invoice_type_id from data since it's already updated
            unset($data['invoice_type_id']);
        }

        // Remove invoice_id from data to prevent manual updating
        unset($data['invoice_id']);

        $invoice->update($data);

        // Update payment_status based on payments
        $totalPaid = $invoice->payments()->sum('payment_amount');
        if ($totalPaid == 0) {
            $invoice->update(['payment_status' => 'unpaid']);
        } elseif ($totalPaid < $invoice->invoice_value) {
            $invoice->update(['payment_status' => 'partial']);
        } else {
            $invoice->update(['payment_status' => 'paid']);
        }

        return response()->json($invoice);
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(string $id): JsonResponse
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted successfully']);
    }

    /**
     * Get invoice summary for projects with year filtering.
     */
    public function invoiceSummary(Request $request): JsonResponse
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

        // 🔹 Query projects
        $projects = Project::query()
            ->with(['client', 'quotation.client', 'paymentRemarks', 'invoices'])
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
                $invoiceTotal = $project->invoices->sum('invoice_value');
                $paymentTotal = $project->invoices->flatMap->payments->sum('payment_amount');
                $outstandingInvoice = $invoiceTotal - $paymentTotal;
                $outstandingAmount = $project->po_value - $paymentTotal;
                $invoiceProgress = $invoiceTotal > 0 ? round(($paymentTotal / $invoiceTotal) * 100, 2) : 0;

                // Get client name from project->client or project->quotation->client
                $clientName = $project->client ? $project->client->name :
                    ($project->quotation && $project->quotation->client ? $project->quotation->client->name : null);

                // Get remarks from payment_remarks
                $remarks = $project->paymentRemarks->pluck('remark')->implode('; ');

                return [
                    'pn_number' => $project->pn_number,
                    'project_name' => $project->project_name,
                    'client_name' => $clientName,
                    'project_value' => $project->po_value,
                    'invoice_total' => $invoiceTotal,
                    'payment_total' => $paymentTotal,
                    'outstanding_invoice' => $outstandingInvoice,
                    'outstanding_amount' => $outstandingAmount,
                    'invoice_progress' => $invoiceProgress,
                    'remarks' => $remarks,
                ];
            });

        return response()->json([
            'availableYears' => $availableYears,
            'year' => $year,
            'range_type' => $rangeType,
            'month' => $month,
            'from_date' => $from,
            'to_date' => $to,
            'projects' => $projects,
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
     * Generate invoice_id based on template IP25020001
     * code_type + full pn_number + invoice number in project
     */
    private function generateInvoiceId(string $projectId, ?int $invoiceTypeId, int $invoiceNumberInProject): string
    {
        $project = Project::findOrFail($projectId);
        $pnNumber = (string)$project->pn_number; // e.g., '25020'

        $codeType = '00'; // default
        if ($invoiceTypeId) {
            $invoiceType = InvoiceType::find($invoiceTypeId);
            if ($invoiceType) {
                $codeType = $invoiceType->code_type;
            }
        }

        return $codeType . $pnNumber . str_pad($invoiceNumberInProject, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get the next invoice number in the project
     */
    private function getInvoiceNumberInProject(string $projectId): int
    {
        $lastInvoice = Invoice::where('project_id', $projectId)
            ->orderBy('invoice_number_in_project', 'desc')
            ->first();

        return $lastInvoice ? $lastInvoice->invoice_number_in_project + 1 : 1;
    }

    /**
     * Get the next invoice_id for a given project and optional invoice type.
     */
    public function nextInvoiceId(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => 'required|string',
            'invoice_type_id' => 'nullable|integer',
            'invoice_sequence' => 'nullable|integer|min:1',
        ]);

        $invoiceNumberInProject = $request->has('invoice_sequence') ? $request->invoice_sequence : $this->getInvoiceNumberInProject($request->project_id);
        $nextInvoiceId = $this->generateInvoiceId($request->project_id, $request->invoice_type_id, $invoiceNumberInProject);

        return response()->json(['next_invoice_id' => $nextInvoiceId]);
    }

    /**
     * Validate if an invoice sequence is available for a project.
     */
    public function validateSequence(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => 'required|string',
            'invoice_sequence' => 'required|integer|min:1',
        ]);

        $exists = Invoice::where('project_id', $request->project_id)
            ->where('invoice_number_in_project', $request->invoice_sequence)
            ->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Sequence already exists for this project' : 'Sequence is available'
        ]);
    }

    /**
     * Display a listing of all invoices with filtering by year and range.
     */
    public function invoiceList(Request $request): JsonResponse
    {
        // 🔹 Ambil daftar tahun dari invoices created_at
        $availableYears = $this->getAvailableYearsInvoices();

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

        // 🔹 Query invoices
        $invoices = Invoice::query()
            ->with([
                'project' => function ($query) {
                    $query->select('pn_number', 'project_name')
                        ->selectRaw('(SELECT c.name FROM quotations q JOIN clients c ON q.client_id = c.id WHERE q.quotation_number = projects.quotations_id) as client_name');
                },
                'invoiceType' => function ($query) {
                    $query->select('id', 'code_type');
                },
                'payments' => function ($query) {
                    $query->select('invoice_id', 'payment_amount');
                }
            ])
            ->orderBy('created_at', 'desc');

        // 🔹 Filter berdasarkan range
        switch ($rangeType) {
            case 'monthly':
                $invoices->whereYear('created_at', $year);
                if ($month) {
                    $invoices->whereMonth('created_at', $month);
                }
                break;

            case 'weekly':
                $invoices->whereBetween('created_at', [
                    now()->startOfWeek(), now()->endOfWeek()
                ]);
                break;

            case 'custom':
                if ($from && $to) {
                    $invoices->whereBetween('created_at', [$from, $to]);
                }
                break;

            default:
                $invoices->whereYear('created_at', $year);
                break;
        }

        $invoices = $invoices->get()
            ->map(function ($invoice) {
                $totalPaymentAmount = $invoice->payments->sum('payment_amount');
                $paymentPercentage = $invoice->invoice_value > 0 ? round(($totalPaymentAmount / $invoice->invoice_value) * 100, 2) : 0;

                return [
                    'invoice_id' => $invoice->invoice_id,
                    'invoice_number_in_project' => $invoice->invoice_number_in_project,
                    'project_id' => $invoice->project_id,
                    'project_name' => $invoice->project ? $invoice->project->project_name : null,
                    'client_name' => $invoice->project ? $invoice->project->client_name : null,
                    'invoice_type' => $invoice->invoiceType ? $invoice->invoiceType->code_type : null,
                    'no_faktur' => $invoice->no_faktur,
                    'invoice_date' => $invoice->invoice_date,
                    'invoice_description' => $invoice->invoice_description,
                    'invoice_value' => $invoice->invoice_value,
                    'invoice_due_date' => $invoice->invoice_due_date,
                    'payment_status' => $invoice->payment_status,
                    'total_payment_amount' => $totalPaymentAmount,
                    'payment_percentage' => $paymentPercentage,
                    'remarks' => $invoice->remarks,
                    'currency' => $invoice->currency,
                    'created_at' => $invoice->created_at,
                ];
            });

        // Calculate totals
        $totalInvoices = $invoices->count();
        $totalInvoiceValue = $invoices->sum('invoice_value');

        return response()->json([
            'status' => 'success',
            'availableYears' => $availableYears,
            'year' => $year,
            'range_type' => $rangeType,
            'month' => $month,
            'from_date' => $from,
            'to_date' => $to,
            'total_invoices' => $totalInvoices,
            'total_invoice_value' => $totalInvoiceValue,
            'data' => $invoices,
        ]);
    }

    /**
     * Ambil daftar tahun yang ada di invoices created_at (unik, ascending)
     */
    private function getAvailableYearsInvoices(): array
    {
        return Invoice::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year')
            ->map(fn($y) => (int)$y)
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Validate invoice creation/update without actually performing the action.
     */
    public function validateInvoice(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => 'required|string',
            'invoice_value' => 'required|numeric',
            'invoice_id' => 'nullable|string', // for updates
        ], [], [
            'project_id' => 'project_id',
            'invoice_value' => 'invoice_value',
            'invoice_id' => 'invoice_id',
        ]);

        $project = Project::findOrFail($request->project_id);

        // Calculate current total for the project
        $currentTotal = Invoice::where('project_id', $request->project_id)->sum('invoice_value');

        // If updating, exclude current invoice
        if ($request->invoice_id) {
            $currentInvoice = Invoice::find($request->invoice_id);
            if ($currentInvoice && $currentInvoice->project_id === $request->project_id) {
                $currentTotal -= $currentInvoice->invoice_value;
            }
        }

        $newTotal = $currentTotal + $request->invoice_value;

        if ($newTotal > $project->po_value) {
            return response()->json([
                'valid' => false,
                'message' => 'Invoice total exceeds project value',
                'current_total' => $currentTotal,
                'new_total' => $newTotal,
                'project_value' => $project->po_value,
                'exceeds_by' => $newTotal - $project->po_value
            ], 200);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Invoice value is within project limits',
            'current_total' => $currentTotal,
            'new_total' => $newTotal,
            'project_value' => $project->po_value
        ]);
    }
}
