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
            ->get()
            ->map(function ($invoice) {
                $invoice->total_payment_amount = $invoice->payments->sum('payment_amount');
                return $invoice;
            });

        return response()->json($invoices);
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
        ]);

        // Fetch project to check value
        $project = Project::findOrFail($request->project_id);

        // Check if adding this invoice would exceed project value
        $currentTotal = Invoice::where('project_id', $request->project_id)->sum('invoice_value');
        if ($currentTotal + $request->invoice_value > $project->po_value) {
            return response()->json(['error' => 'Invoice total exceeds project value'], 400);
        }

        // Generate invoice_id based on template IP25001
        $invoiceId = $this->generateInvoiceId($request->project_id, $request->invoice_type_id);

        // Get invoice number in project
        $invoiceNumberInProject = $this->getInvoiceNumberInProject($request->project_id);

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

                // Get client name from project->client or project->quotation->client
                $clientName = $project->client ? $project->client->name :
                    ($project->quotation && $project->quotation->client ? $project->quotation->client->name : null);

                // Get remarks from payment_remarks
                $remarks = $project->paymentRemarks->pluck('remark')->implode('; ');

                return [
                    'pn_number' => $project->pn_number,
                    'project_name' => $project->project_name,
                    'client' => $clientName,
                    'project_value' => $project->po_value,
                    'invoice_total' => $invoiceTotal,
                    'payment_total' => $paymentTotal,
                    'outstanding_invoice' => $outstandingInvoice,
                    'outstanding_amount' => $outstandingAmount,
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
     * Generate invoice_id based on template IP25001
     * IP + code_type + year from pn_number + invoice number in project
     */
    private function generateInvoiceId(string $projectId, ?int $invoiceTypeId): string
    {
        $project = Project::findOrFail($projectId);
        $yearShort = substr((string)$project->pn_number, 0, 2); // e.g., '25' for 2025

        $codeType = '00'; // default
        if ($invoiceTypeId) {
            $invoiceType = InvoiceType::find($invoiceTypeId);
            if ($invoiceType) {
                $codeType = $invoiceType->code_type;
            }
        }

        $invoiceNumberInProject = $this->getInvoiceNumberInProject($projectId);

        return $codeType . $yearShort . str_pad($invoiceNumberInProject, 3, '0', STR_PAD_LEFT);
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
        ]);

        $nextInvoiceId = $this->generateInvoiceId($request->project_id, $request->invoice_type_id);

        return response()->json(['next_invoice_id' => $nextInvoiceId]);
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
