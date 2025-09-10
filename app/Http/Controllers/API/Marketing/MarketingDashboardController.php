<?php

namespace App\Http\Controllers\API\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingDashboardController extends Controller
{
    //
    public function index()
    {
        // Statistik utama
        $totalQuotation = Quotation::count();
        $totalQuotationValue = Quotation::sum('quotation_value');
        $totalSalesValue = Project::sum('po_value');
        $totalProject = Project::count();
        $outstandingQuotation = Quotation::where('status', 'O')->count();

        $allStatusKeys = ['A', 'D', 'E', 'F', 'O'];

        $quotationStatusCount = collect($allStatusKeys)->mapWithKeys(fn($key) => [
            $key => Quotation::where('status', $key)->count()
        ]);

        // Label dan warna status
        $statusLabels = [
            'A' => 'Quotation and PO Completed',
            'D' => 'Project belum ada PO',
            'E' => 'Penawaran Project Batal',
            'F' => 'Penawaran Project Kalah',
            'O' => 'On Going',
        ];

        $statusColors = [
            'A' => '#10b981',
            'D' => '#3b82f6',
            'E' => '#f59e0b',
            'F' => '#ef4444',
            'O' => '#a855f7',
        ];

        $labels = collect($allStatusKeys)->map(fn($k) => $statusLabels[$k]);
        $colors = collect($allStatusKeys)->map(fn($k) => $statusColors[$k]);
        $data = collect($allStatusKeys)->map(fn($k) => $quotationStatusCount[$k]);

        // Bulan untuk grafik
        $months = collect(range(1, 12))->map(fn($m) => date('M', mktime(0, 0, 0, $m, 1)));

        // Data quotation per bulan
        $quotationPerMonth = Quotation::select(
            DB::raw('MONTH(quotation_date) as month_num'),
            DB::raw('COALESCE(SUM(quotation_value), 0) as total')
        )
        ->whereYear('quotation_date', now()->year)
        ->groupBy(DB::raw('MONTH(quotation_date)'))
        ->pluck('total', 'month_num')
        ->toArray();

        // Data sales per bulan
        $salesPerMonth = Project::select(
            DB::raw('MONTH(COALESCE(po_date, created_at)) as month_num'),
            DB::raw('COALESCE(SUM(po_value), 0) as total')
        )
        ->where(function($query) {
            $query->whereNotNull('po_value')->where('po_value', '>', 0);
        })
        ->whereYear(DB::raw('COALESCE(po_date, created_at)'), now()->year)
        ->groupBy(DB::raw('MONTH(COALESCE(po_date, created_at))'))
        ->pluck('total', 'month_num')
        ->toArray();

        // Normalisasi data (isi 0 untuk bulan kosong)
        $quotationPerMonthData = [];
        $salesPerMonthData = [];
        foreach (range(1, 12) as $m) {
            $quotationPerMonthData[] = $quotationPerMonth[$m] ?? 0;
            $salesPerMonthData[] = $salesPerMonth[$m] ?? 0;
        }

        return response()->json([
            'totalQuotation' => $totalQuotation,
            'totalQuotationValue' => $totalQuotationValue,
            'totalSalesValue' => $totalSalesValue,
            'totalProject' => $totalProject,
            'outstandingQuotation' => $outstandingQuotation,
            'months' => $months,
            'quotationPerMonthData' => $quotationPerMonthData,
            'salesPerMonthData' => $salesPerMonthData,
            'quotationStatusCount' => $quotationStatusCount,
            'statusLabels' => $statusLabels,
            'statusColors' => $statusColors,
            'labels' => $labels,
            'colors' => $colors,
            'data' => $data,
            'role' => auth('api')->user()->role->name
        ]);
    }
}
