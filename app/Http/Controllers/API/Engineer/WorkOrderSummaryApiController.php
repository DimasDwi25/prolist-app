<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkOrderSummaryApiController extends Controller
{
    //
    public function workOrderSummary(Request $request)
    {
        // 🔹 Ambil daftar tahun dari kolom wo_date
        $availableYears = $this->getAvailableYears();

        // 🔹 Tahun aktif (default: tahun terakhir yang tersedia, atau tahun sekarang)
        $yearParam = $request->query('year');
        $year = $yearParam
            ? (int) $yearParam
            : (!empty($availableYears) ? end($availableYears) : now()->year);

        // 🔹 Ambil filter tambahan
        $rangeType = $request->query('range_type', 'yearly');
        $month = $request->query('month');
        $from = $request->query('from_date');
        $to = $request->query('to_date');

        // 🔹 Query dasar
        $workOrders = WorkOrder::query()
            ->with(['project.client'])
            ->whereNotNull('wo_date')
            ->where('status', WorkOrder::STATUS_FINISHED);

        // 🔹 Filter berdasarkan rentang waktu
        switch ($rangeType) {
            case 'monthly':
                $workOrders->whereYear('wo_date', $year);
                if ($month) {
                    $workOrders->whereMonth('wo_date', $month);
                }
                break;

            case 'weekly':
                $workOrders->whereBetween('wo_date', [
                    now()->startOfWeek(), now()->endOfWeek(),
                ]);
                break;

            case 'custom':
                if ($from && $to) {
                    $workOrders->whereBetween('wo_date', [$from, $to]);
                }
                break;

            default: // yearly
                $workOrders->whereYear('wo_date', $year);
                break;
        }

        // 🔹 Hitung summary
        $totalFinished = (clone $workOrders)->count();
        $totalMandays = (clone $workOrders)->sum('total_mandays_eng') + (clone $workOrders)->sum('total_mandays_elect');
        $averageMandays = round($totalMandays / max($totalFinished, 1), 2);

        // 🔹 Ambil data list WO
        $woList = $workOrders
            ->orderByDesc('wo_date')
            ->get([
                'id',
                'project_id',
                'purpose_id',
                'wo_kode_no',
                'wo_number_in_project',
                'total_mandays_eng',
                'total_mandays_elect',
                'status',
                'wo_date',
            ]);

        // 🔹 Format data untuk frontend
        $workOrderData = $woList->map(fn($wo) => [
            'wo_id' => $wo->id,
            'wo_code' => "{$wo->wo_kode_no}-{$wo->wo_number_in_project}",
            'project_number' => $wo->project->project_number ?? '-',
            'project_name' => $wo->project->project_name ?? '-',
            'client_name' => $wo->project->client->name ?? $wo->project->quotation->client->name,
            'total_mandays' => ($wo->total_mandays_eng ?? 0) + ($wo->total_mandays_elect ?? 0),
            'status' => ucfirst($wo->status ?? '-'),
            'wo_date' => $wo->wo_date ? Carbon::parse($wo->wo_date)->format('Y-m-d') : null,
        ]);

        // 🔹 Response JSON
        return response()->json([
            'availableYears' => $availableYears,
            'filters' => [
                'year' => $year,
                'range_type' => $rangeType,
                'month' => $month,
                'from' => $from,
                'to' => $to,
            ],
            'summary' => [
                'total_finished' => $totalFinished,
                'total_mandays' => $totalMandays,
                'average_mandays' => $averageMandays,
            ],
            'work_orders' => $workOrderData,
        ]);
    }

    /**
     * Ambil daftar tahun unik dari kolom wo_date
     */
    private function getAvailableYears(): array
    {
        return WorkOrder::whereNotNull('wo_date')
            ->selectRaw('DISTINCT YEAR(wo_date) as year')
            ->orderBy('year', 'asc')
            ->pluck('year')
            ->map(fn($y) => (int) $y)
            ->values()
            ->toArray();
    }
}
