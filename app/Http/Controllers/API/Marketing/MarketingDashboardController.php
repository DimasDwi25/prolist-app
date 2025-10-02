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
    // public function index(Request $request)
    // {
    //     // Ambil tahun dari query param, default sekarang
    //     $yearParam = $request->query('year');
    //     $currentYear = now()->year;
    //     $year = $yearParam ? 2000 + (int)$yearParam : $currentYear; // '24' -> 2024

    //     // Statistik utama, filter Project & Quotation berdasarkan pn_number
    //     $totalQuotation = Quotation::whereYearFromQuotation($year)->count();
    //     $totalQuotationValue = Quotation::whereYearFromQuotation($year)->sum('quotation_value');
    //     $totalSalesValue = Project::whereYearFromPn($year)->sum('po_value');
    //     $totalProject = Project::whereYearFromPn($year)->count();
    //     $outstandingQuotation = Quotation::where('status', 'O')->whereYearFromQuotation($year)->count();

    //     // Status quotation
    //     $allStatusKeys = ['A','D','E','F','O'];
    //     $quotationStatusCount = collect($allStatusKeys)->mapWithKeys(fn($key) => [
    //         $key => Quotation::where('status', $key)
    //             ->whereYearFromQuotation($year) // <- pakai scope yang benar
    //             ->count()
    //     ]);


    //     // Status labels & colors
    //     $statusLabels = ['A'=>'Quotation and PO Completed','D'=>'Project belum ada PO','E'=>'Penawaran Project Batal','F'=>'Penawaran Project Kalah','O'=>'On Going'];
    //     $statusColors = ['A'=>'#10b981','D'=>'#3b82f6','E'=>'#f59e0b','F'=>'#ef4444','O'=>'#a855f7'];

    //     $labels = collect($allStatusKeys)->map(fn($k) => $statusLabels[$k]);
    //     $colors = collect($allStatusKeys)->map(fn($k) => $statusColors[$k]);
    //     $data = collect($allStatusKeys)->map(fn($k) => $quotationStatusCount[$k]);

    //     // Bulan untuk grafik
    //     $months = collect(range(1,12))->map(fn($m) => date('M', mktime(0,0,0,$m,1)));

    //     // Quotation per bulan
    //     $quotationPerMonth = Quotation::whereYearFromQuotation($year)
    //     ->select(DB::raw('MONTH(quotation_date) as month_num'), DB::raw('COALESCE(SUM(quotation_value),0) as total'))
    //     ->groupBy(DB::raw('MONTH(quotation_date)'))
    //     ->pluck('total','month_num')
    //     ->toArray();


    //     // Sales per bulan
    //     $salesPerMonth = Project::whereYearFromPn($year)
    //         ->select(DB::raw('MONTH(COALESCE(po_date, created_at)) as month_num'), DB::raw('COALESCE(SUM(po_value),0) as total'))
    //         ->whereNotNull('po_value')->where('po_value','>',0)
    //         ->groupBy(DB::raw('MONTH(COALESCE(po_date, created_at))'))
    //         ->pluck('total','month_num')
    //         ->toArray();

    //     $quotationPerMonthData = [];
    //     $salesPerMonthData = [];
    //     foreach(range(1,12) as $m){
    //         $quotationPerMonthData[] = $quotationPerMonth[$m] ?? 0;
    //         $salesPerMonthData[] = $salesPerMonth[$m] ?? 0;
    //     }

    //     return response()->json([
    //         'year' => $year,
    //         'totalQuotation'=>$totalQuotation,
    //         'totalQuotationValue'=>$totalQuotationValue,
    //         'totalSalesValue'=>$totalSalesValue,
    //         'totalProject'=>$totalProject,
    //         'outstandingQuotation'=>$outstandingQuotation,
    //         'months'=>$months,
    //         'quotationPerMonthData'=>$quotationPerMonthData,
    //         'salesPerMonthData'=>$salesPerMonthData,
    //         'quotationStatusCount'=>$quotationStatusCount,
    //         'statusLabels'=>$statusLabels,
    //         'statusColors'=>$statusColors,
    //         'labels'=>$labels,
    //         'colors'=>$colors,
    //         'data'=>$data,
    //         'role'=>auth('api')->user()->role->name
    //     ]);
    // }

    public function index(Request $request)
    {
        $yearParam = $request->query('year');
        $availableYears = $this->getYears();
        $year = $yearParam ? (int)$yearParam : (!empty($availableYears) ? end($availableYears) : now()->year);

        $rangeType = $request->query('range_type', 'monthly'); // monthly, weekly, custom
        $monthParam = $request->query('month'); // 1-12
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        // Inisialisasi query
        $quotations = Quotation::query();
        $projects = Project::query();

        // Filter berdasarkan range type
        if ($rangeType === 'monthly' && $monthParam) {
            $quotations->whereYear('quotation_date', $year)
                    ->whereMonth('quotation_date', $monthParam);
            $projects->whereYear(DB::raw('COALESCE(po_date, created_at)'), $year)
                    ->whereMonth(DB::raw('COALESCE(po_date, created_at)'), $monthParam);
        } elseif ($rangeType === 'weekly') {
            $quotations->whereBetween('quotation_date', [now()->startOfWeek(), now()->endOfWeek()]);
            $projects->whereBetween(DB::raw('COALESCE(po_date, created_at)'), [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($rangeType === 'custom' && $fromDate && $toDate) {
            $quotations->whereBetween('quotation_date', [$fromDate, $toDate]);
            $projects->whereBetween(DB::raw('COALESCE(po_date, created_at)'), [$fromDate, $toDate]);
        } else {
            // default: filter berdasarkan tahun saja
            $quotations->whereYearFromQuotation($year);
            $projects->whereYearFromPn($year);
        }

        // Statistik
        $totalQuotation = $quotations->count();
        $totalQuotationValue = $quotations->sum('quotation_value');
        $totalSalesValue = $projects->sum('po_value');
        $totalProject = $projects->count();
        $outstandingQuotation = $quotations->where('status', 'O')->count();

        // Status distribution
        $allStatusKeys = ['A','D','E','F','O'];
        $quotationStatusCount = collect($allStatusKeys)->mapWithKeys(fn($key) => [
            $key => Quotation::where('status', $key)
                ->whereYearFromQuotation($year) // <- pakai scope yang benar
                ->count()
        ]);

        // Monthly data (jika range = monthly)
        $months = collect(range(1,12))->map(fn($m) => date('M', mktime(0,0,0,$m,1)));
        $quotationPerMonth = (clone $quotations)
            ->select(DB::raw('MONTH(quotation_date) as month_num'), DB::raw('COALESCE(SUM(quotation_value),0) as total'))
            ->groupBy(DB::raw('MONTH(quotation_date)'))
            ->pluck('total','month_num')
            ->toArray();
        $salesPerMonth = (clone $projects)
            ->select(DB::raw('MONTH(COALESCE(po_date, created_at)) as month_num'), DB::raw('COALESCE(SUM(po_value),0) as total'))
            ->groupBy(DB::raw('MONTH(COALESCE(po_date, created_at))'))
            ->pluck('total','month_num')
            ->toArray();

        $quotationPerMonthData = [];
        $salesPerMonthData = [];
        foreach(range(1,12) as $m){
            $quotationPerMonthData[] = $quotationPerMonth[$m] ?? 0;
            $salesPerMonthData[] = $salesPerMonth[$m] ?? 0;
        }

        return response()->json([
            'availableYears' => $availableYears,
            'year' => $year,
            'totalQuotation'=>$totalQuotation,
            'totalQuotationValue'=>$totalQuotationValue,
            'totalSalesValue'=>$totalSalesValue,
            'totalProject'=>$totalProject,
            'outstandingQuotation'=>$outstandingQuotation,
            'months'=>$months,
            'quotationPerMonthData'=>$quotationPerMonthData,
            'salesPerMonthData'=>$salesPerMonthData,
            'quotationStatusCount'=>$quotationStatusCount,
            'labels'=>collect($allStatusKeys)->map(fn($k) => [
                'A'=>'Quotation and PO Completed',
                'D'=>'Project belum ada PO',
                'E'=>'Penawaran Project Batal',
                'F'=>'Penawaran Project Kalah',
                'O'=>'On Going'
            ][$k]),
            'colors'=>collect($allStatusKeys)->map(fn($k) => [
                'A'=>'#10b981','D'=>'#3b82f6','E'=>'#f59e0b','F'=>'#ef4444','O'=>'#a855f7'
            ][$k]),
            'data'=>collect($allStatusKeys)->map(fn($k) => $quotationStatusCount[$k]),
            'role'=>auth('api')->user()->role->name
        ]);
    }



    public function getYears(): array
    {
        // Dari Project
        $projectYears = Project::selectRaw('LEFT(pn_number, 2) as year_short')
            ->distinct()
            ->pluck('year_short')
            ->map(fn($y) => 2000 + (int)$y)
            ->toArray();

        // Dari Quotation
        $quotationYears = Quotation::selectRaw('LEFT(quotation_number, 4) as year_full')
            ->distinct()
            ->pluck('year_full')
            ->map(fn($y) => (int)$y)
            ->toArray();

        // Gabungkan dan unique
        return collect(array_merge($projectYears, $quotationYears))
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

}
