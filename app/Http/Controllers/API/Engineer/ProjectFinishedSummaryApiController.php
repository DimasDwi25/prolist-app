<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectFinishedSummaryApiController extends Controller
{
    //
    public function finishedSummary(Request $request)
    {
        // 🔹 Ambil daftar tahun dari project_finish_date
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

        // 🔹 Query project finished
        $projects = Project::query()
            ->with(['statusProject', 'client', 'quotation.client'])
            ->whereHas('statusProject', fn($q) => $q->where('name', 'Project Finished'))
            ->whereNotNull('project_finish_date');

        // 🔹 Filter berdasarkan range
        switch ($rangeType) {
            case 'monthly':
                $projects->whereYear('project_finish_date', $year);
                if ($month) {
                    $projects->whereMonth('project_finish_date', $month);
                }
                break;

            case 'weekly':
                $projects->whereBetween('project_finish_date', [
                    now()->startOfWeek(), now()->endOfWeek()
                ]);
                break;

            case 'custom':
                if ($from && $to) {
                    $projects->whereBetween('project_finish_date', [$from, $to]);
                }
                break;

            default:
                $projects->whereYear('project_finish_date', $year);
                break;
        }


        // 🔹 Summary
        $totalFinished = (clone $projects)->count();
        $totalPoValue = (clone $projects)->sum('po_value');
        $averageProgress = round((clone $projects)->avg('project_progress'), 2);

        // 🔹 List project
        $projectList = $projects
            ->orderByDesc('project_finish_date')
            ->get([
                'pn_number',
                'project_name',
                'po_value',
                'project_progress',
                'status_project_id',
                'client_id',
                'quotations_id',
                'project_finish_date',
            ]);

        // 🔹 Format untuk frontend
        $projectData = $projectList->map(fn($p) => [
            'pn_number' => $p->pn_number,
            'project_name' => $p->project_name,
            'po_value' => $p->po_value,
            'project_progress' => $p->project_progress,
            'status' => $p->statusProject->name ?? '-',
            'client_name' => $p->client->name ?? $p->quotation->client->name ?? '-',
            'project_finish_date' => $p->project_finish_date,
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
                'total_po_value' => $totalPoValue,
                'average_progress' => $averageProgress,
            ],
            'projects' => $projectData,
        ]);
    }

    /**
     * Ambil daftar tahun yang ada di project_finish_date (unik, ascending)
     */
    private function getAvailableYears(): array
    {
        return Project::whereNotNull('project_finish_date')
            ->selectRaw('DISTINCT YEAR(project_finish_date) as year')
            ->orderBy('year', 'asc')
            ->pluck('year')
            ->map(fn($y) => (int) $y)
            ->values()
            ->toArray();
    }


}
