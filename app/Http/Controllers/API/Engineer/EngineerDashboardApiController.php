<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\StatusProject;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EngineerDashboardApiController extends Controller
{
    //
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth   = $now->copy()->endOfMonth();

        // === 1. KPI ===
        $projectOverdue = Project::whereNotNull('target_dates')
            ->where('target_dates', '<', $now)
            ->whereNull('engineering_finish_date')
            ->count();

        $projectDueThisMonth = Project::whereNotNull('target_dates')
            ->whereMonth('target_dates', $now->month)
            ->whereYear('target_dates', $now->year)
            ->count();

        $projectOnTrack = Project::whereNotNull('target_dates')
            ->where('target_dates', '>=', $now)
            ->whereNull('engineering_finish_date')
            ->count();

        $totalActiveProjects = Project::count();

        // === 2. Chart ===
        // Line Chart: Completed projects per month (last 12 months)
        $completedProjects = Project::select([
            DB::raw("YEAR(engineering_finish_date) as year"),
            DB::raw("MONTH(engineering_finish_date) as month"),
            DB::raw("COUNT(*) as total")
        ])
        ->whereNotNull('engineering_finish_date')
        ->where('engineering_finish_date', '>=', $now->copy()->subMonths(11)->startOfMonth())
        ->groupBy(DB::raw("YEAR(engineering_finish_date), MONTH(engineering_finish_date)"))
        ->orderBy(DB::raw("MIN(engineering_finish_date)"))
        ->get()
        ->mapWithKeys(function ($row) {
            $month = sprintf("%04d-%02d", $row->year, $row->month);
            return [$month => $row->total];
        });


        $months = collect(range(0, 11))->map(function ($i) use ($now, $completedProjects) {
            $month = $now->copy()->subMonths(11 - $i)->format('Y-m');
            return [
                'label' => $now->copy()->subMonths(11 - $i)->format('M Y'),
                'value' => $completedProjects[$month] ?? 0,
            ];
        });

        // Pie Chart: Status Distribution
        $statusCounts = Project::select('status_project_id', DB::raw('COUNT(*) as total'))
            ->groupBy('status_project_id')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$row->status_project_id => $row->total];
            });

        $statusLabels = StatusProject::whereIn('id', $statusCounts->keys())->pluck('name', 'id');

        // === 4. Utilization Chart (Mandays) ===
        $utilization = [
            [
                'role' => 'Engineer',
                'used' => WorkOrder::whereMonth('wo_date', now()->month)
                    ->whereYear('wo_date', now()->year)
                    ->sum('total_mandays_eng'),
            ],
            [
                'role' => 'Electrician',
                'used' => WorkOrder::whereMonth('wo_date', now()->month)
                    ->whereYear('wo_date', now()->year)
                    ->sum('total_mandays_elect'),
            ],
        ];

        $totalWorkOrders = WorkOrder::whereMonth('wo_date', now()->month)
            ->whereYear('wo_date', now()->year)
            ->count();

        $upcomingProjects = Project::whereNotNull('target_dates')
        ->whereBetween('target_dates', [$now, $now->copy()->addDays(30)])
        ->with('statusProject')
        ->orderBy('target_dates')
        ->get()
        ->map(function ($p) {
            return [
                'pn_number'    => $p->pn_number,
                'project_name' => $p->project_name,
                'target_dates' => $p->target_dates,
                'status'       => $p->statusProject->name ?? '-',
            ];
        });



        // === 3. Top 5 Overdue Projects ===
        $top5Overdue = Project::whereNotNull('target_dates')
        ->where('target_dates', '<', $now)
        ->whereNull('engineering_finish_date')
        ->whereHas('statusProject', function ($q) {
            $q->where('name', '!=', 'Project Finished');
        })
        ->with([
            'statusProject',
            'phc.picEngineering', // eager load relasi ke User
        ])
        ->get()
        ->map(function ($p) use ($now) {
            return [
                'pn_number'    => $p->pn_number,
                'project_name' => $p->project_name,
                'target_dates' => $p->target_dates,
                'delay_days'   => Carbon::parse($p->target_dates)->diffInDays($now),
                'status'       => $p->statusProject->name ?? '-',
                'pic'          => $p->phc?->picEngineering?->name ?? '-', // safe null check
            ];
        })
        ->sortByDesc('delay_days')
        ->take(5)
        ->values();


        return response()->json([
            'projectOverdue'      => $projectOverdue,
            'projectDueThisMonth' => $projectDueThisMonth,
            'projectOnTrack'      => $projectOnTrack,
            'totalActiveProjects' => $totalActiveProjects,

            'months'             => $months->pluck('label'),
            'completedProjects'  => $months->pluck('value'),

            'statusLabels' => $statusLabels->values(),
            'statusCounts' => $statusLabels->keys()->map(fn ($id) => $statusCounts[$id] ?? 0),

            'top5Overdue'      => $top5Overdue,
            'upcomingProjects' => $upcomingProjects,
            'utilization'      => $utilization,
            'total_work_orders' => $totalWorkOrders,
        ]);

    }
}
