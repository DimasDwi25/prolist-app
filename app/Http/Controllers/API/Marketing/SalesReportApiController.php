<?php

namespace App\Http\Controllers\API\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportApiController extends Controller
{
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
        $projects = Project::with(['category', 'quotation.client', 'client', 'statusProject']);

        // Filter berdasarkan range type
        if ($rangeType === 'monthly' && $monthParam) {
            $projects->whereYearFromPn($year)
                    ->whereMonth('po_date', $monthParam);
        } elseif ($rangeType === 'weekly') {
            $projects->whereBetween('po_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($rangeType === 'custom' && $fromDate && $toDate) {
            $projects->whereBetween('po_date', [$fromDate, $toDate]);
        } else {
            // default: filter berdasarkan tahun saja
            $projects->whereYearFromPn($year);
        }

        $projects = $projects->orderByRaw('CAST(LEFT(CAST(pn_number AS VARCHAR), 2) AS INT) DESC') // ambil 2 digit pertama sebagai tahun
        ->orderByRaw('CAST(SUBSTRING(CAST(pn_number AS VARCHAR), 3, LEN(CAST(pn_number AS VARCHAR)) - 2) AS INT) DESC') // ambil nomor urut
        ->get();

        // Calculate total project value from filtered projects
        $totalProjectValue = $projects->sum(function ($project) {
            return $project->quotation ? $project->quotation->quotation_value : 0;
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Sales report fetched successfully',
            'data'    => $projects,
            'totalProjectValue' => $totalProjectValue,
            'filters' => [
                'year' => $year,
                'range_type' => $rangeType,
                'month' => $monthParam,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'available_years' => $availableYears,
            ],
        ]);
    }

    public function getYears(): array
    {
        // Dari Project pn_number
        $projectYears = Project::selectRaw('LEFT(CONVERT(VARCHAR(20), pn_number), 2) as year_short')
            ->distinct()
            ->pluck('year_short')
            ->map(fn($y) => (int)('20' . $y)) // tambah 20 untuk full year
            ->toArray();

        // Gabungkan dan unique
        return collect($projectYears)
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }
}
