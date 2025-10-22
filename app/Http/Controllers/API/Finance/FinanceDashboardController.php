<?php

namespace App\Http\Controllers\API\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\RequestInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get year from query param, default to current year
        $yearParam = $request->query('year');
        $currentYear = now()->year;
        $year = $yearParam ? (int)$yearParam : $currentYear;

        // Request invoice count
        $requestInvoiceCount = RequestInvoice::count();

        // Jumlah PN (projects)
        $projectCount = Project::whereYearFromPn($year)->count();

        // Total invoice value
        $totalInvoice = Invoice::whereHas('project', function ($query) use ($year) {
            $query->whereYearFromPn($year);
        })->sum('invoice_value');

        // Invoice outstanding (total invoice - total payments)
        $invoiceOutstanding = Invoice::whereHas('project', function ($query) use ($year) {
            $query->whereYearFromPn($year);
        })->with('payments')->get()->sum(function ($invoice) {
            return $invoice->invoice_value - $invoice->payments->sum('payment_amount');
        });

        // Invoice due date - count of invoices past due
        $invoiceDueCount = Invoice::whereHas('project', function ($query) use ($year) {
            $query->whereYearFromPn($year);
        })->where('invoice_due_date', '<', now())->count();

        // Summary invoice vs PN with payment < 100%
        $incompletePayments = Project::whereYearFromPn($year)
            ->with(['invoices.payments'])
            ->get()
            ->filter(function ($project) {
                $totalInvoice = $project->invoices->sum('invoice_value');
                $totalPayment = $project->invoices->flatMap->payments->sum('payment_amount');
                return $totalInvoice > 0 && ($totalPayment / $totalInvoice) < 1;
            })
            ->map(function ($project) {
                $totalInvoice = $project->invoices->sum('invoice_value');
                $totalPayment = $project->invoices->flatMap->payments->sum('payment_amount');
                $percentage = $totalInvoice > 0 ? ($totalPayment / $totalInvoice) * 100 : 0;

                return [
                    'pn_number' => $project->pn_number,
                    'project_name' => $project->project_name,
                    'total_invoice' => $totalInvoice,
                    'total_payment' => $totalPayment,
                    'payment_percentage' => round($percentage, 2),
                ];
            });

        return response()->json([
            'year' => $year,
            'request_invoice' => $requestInvoiceCount,
            'jumlah_pn' => $projectCount,
            'total_invoice' => $totalInvoice,
            'invoice_outstanding' => $invoiceOutstanding,
            'invoice_due_count' => $invoiceDueCount,
            'incomplete_payments_summary' => $incompletePayments,
        ]);
    }
}
