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

        $allStatusKeys = ['A', 'D', 'E', 'F', 'O'];

        $quotationStatusCount = collect($allStatusKeys)->mapWithKeys(function ($key) {
            return [$key => \App\Models\Quotation::where('status', $key)->count()];
        });

        // Mapping label untuk status
        $statusLabels = [
            'A' => 'Quotation and PO Completed',
            'D' => 'Project belum ada PO',
            'E' => 'Penawaran Project Batal',
            'F' => 'Penawaran Project Kalah',
            'O' => 'On Going',
        ];

        // Warna untuk pie chart
        $statusColors = [
            'A' => '#10b981', // Green
            'D' => '#3b82f6', // Blue
            'E' => '#f59e0b', // Amber
            'F' => '#ef4444', // Red
            'O' => '#a855f7', // Purple
        ];

        $labels = collect($allStatusKeys)->map(fn($k) => $statusLabels[$k]);
        $colors = collect($allStatusKeys)->map(fn($k) => $statusColors[$k]);
        $data = collect($allStatusKeys)->map(fn($k) => $quotationStatusCount[$k]);

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
            'salesPerMonthData',
            'quotationStatusCount',
            'statusLabels',
            'statusColors',
            'labels', // baru
            'colors', // baru
            'data'    // baru
        ));
    }
}
