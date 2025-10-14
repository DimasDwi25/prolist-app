<?php

namespace App\Http\Controllers\API\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingReportApiController extends Controller
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
        $quotations = Quotation::with('client', 'user');

        // Filter berdasarkan range type
        if ($rangeType === 'monthly' && $monthParam) {
            $quotations->whereYear('quotation_date', $year)
                    ->whereMonth('quotation_date', $monthParam);
        } elseif ($rangeType === 'weekly') {
            $quotations->whereBetween('quotation_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($rangeType === 'custom' && $fromDate && $toDate) {
            $quotations->whereBetween('quotation_date', [$fromDate, $toDate]);
        } else {
            // default: filter berdasarkan tahun saja
            $quotations->whereYearFromQuotation($year);
        }

        $quotations = $quotations->orderByRaw('CAST(LEFT(quotation_number, 4) AS INT) DESC') // tahun DESC
            ->orderByRaw('CAST(SUBSTRING(quotation_number, 5, LEN(quotation_number) - 4) AS INT) DESC') // nomor per tahun DESC
            ->get();

        // Calculate total quotation value from filtered quotations
        $totalQuotationValue = $quotations->sum('quotation_value');

        return response()->json([
            'status'  => 'success',
            'message' => 'Sales report fetched successfully',
            'data'    => $quotations,
            'totalQuotationValue' => $totalQuotationValue,
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
        // Dari Quotation
        $quotationYears = Quotation::selectRaw('LEFT(quotation_number, 4) as year_full')
            ->distinct()
            ->pluck('year_full')
            ->map(fn($y) => (int)$y)
            ->toArray();

        // Gabungkan dan unique
        return collect($quotationYears)
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }
}
