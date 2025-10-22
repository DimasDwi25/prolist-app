<?php

namespace App\Http\Controllers\MarketingDirector;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        // DB::listen(function ($query) {
        //     // Simpan ke laravel.log
        //     Log::info('SQL: ' . $query->sql, $query->bindings);
        // });
        // Statistik utama
        $totalQuotation = Quotation::count();
        $totalQuotationValue = Quotation::sum('quotation_value');
        $totalSalesValue = Project::sum('po_value');
        $totalProject = Project::count();
        $outstandingQuotation = Quotation::where('status', 'O')->count();

        $allStatusKeys = ['A', 'D', 'E', 'F', 'O'];

        $quotationStatusCount = collect($allStatusKeys)->mapWithKeys(function ($key) {
            return [$key => Quotation::where('status', $key)->count()];
        });

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
        $months = collect(range(1, 12))->map(fn($m) => date('M', mktime(0, 0, 0, $m, 1)))->toArray();

        // Data quotation per bulan (tanpa ORDER BY di SQL)
        $quotationPerMonth = Quotation::select(
            DB::raw('MONTH(quotation_date) as month_num'),
            DB::raw('SUM(quotation_value) as total')
        )
            ->whereYear('quotation_date', now()->year)
            ->groupBy(DB::raw('MONTH(quotation_date)'))
            ->pluck('total', 'month_num')
            ->toArray();

        // Data sales per bulan (tanpa ORDER BY di SQL)
        $salesPerMonth = Project::select(
            DB::raw('MONTH(po_date) as month_num'),
            DB::raw('SUM(po_value) as total')
        )
            ->whereYear('po_date', now()->year)
            ->groupBy(DB::raw('MONTH(po_date)'))
            ->pluck('total', 'month_num')
            ->toArray();

        // Urutkan secara manual di PHP
        ksort($quotationPerMonth);
        ksort($salesPerMonth);

        // Normalisasi data (isi 0 untuk bulan kosong)
        $quotationPerMonthData = [];
        $salesPerMonthData = [];
        foreach (range(1, 12) as $m) {
            $quotationPerMonthData[] = $quotationPerMonth[$m] ?? 0;
            $salesPerMonthData[] = $salesPerMonth[$m] ?? 0;
        }

        return view('marketing-director.dashboard', compact(
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
            'labels',
            'colors',
            'data'
        ));
    }
}
