<?php

namespace App\Http\Controllers\supervisor_marketing;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class SupervisorDashboardController extends Controller
{
    public function index()
    {
        // Statistik utama
        $totalQuotation = Quotation::count();
        $totalQuotationValue = Quotation::sum('quotation_value');
        $totalSalesValue = Quotation::sum('po_value');
        $totalProject = Project::count();
        $outstandingQuotation = Quotation::where('status', 'O')->count();

        // Ambil tren bulanan (12 bulan terakhir)
        $months = collect(range(1, 12))->map(function ($m) {
            return date('M', mktime(0, 0, 0, $m, 1));
        })->toArray();

        $quotationPerMonth = Quotation::select(
                DB::raw('MONTH(quotation_date) as month'),
                DB::raw('SUM(quotation_value) as total')
            )
            ->whereYear('quotation_date', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $salesPerMonth = Quotation::select(
                DB::raw('MONTH(po_date) as month'),
                DB::raw('SUM(po_value) as total')
            )
            ->whereYear('po_date', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Normalize data (isi 0 untuk bulan yang tidak ada data)
        $quotationPerMonthData = [];
        $salesPerMonthData = [];
        foreach (range(1, 12) as $m) {
            $quotationPerMonthData[] = $quotationPerMonth[$m] ?? 0;
            $salesPerMonthData[] = $salesPerMonth[$m] ?? 0;
        }

        return view('supervisor.dashboard', compact(
            'totalQuotation',
            'totalQuotationValue',
            'totalSalesValue',
            'totalProject',
            'outstandingQuotation',
            'months',
            'quotationPerMonthData',
            'salesPerMonthData'
        ));
    }
}
