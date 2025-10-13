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

        $kpis = $this->getKpis($now);
        $charts = $this->getCharts($now);
        $workOrders = $this->getWorkOrdersThisMonth();
        $projects = $this->getProjectLists($now);

        return response()->json(array_merge($kpis, $charts, $projects, $workOrders));
    }

    private function getKpis(Carbon $now)
    {
        $overdue = $this->getProjectsByCriteria($now, function($q) use ($now) {
            $q->whereNotNull('target_finish_date')->where('target_finish_date', '<', $now);
        }, true);

        $dueThisMonth = $this->getProjectsByCriteria($now, function($q) use ($now) {
            $q->whereNotNull('target_finish_date')
              ->whereMonth('target_finish_date', $now->month)
              ->whereYear('target_finish_date', $now->year);
        }, false);

        $onTrack = $this->getProjectsByCriteria($now, function($q) use ($now) {
            $q->whereNotNull('target_finish_date')->where('target_finish_date', '>=', $now);
        }, true);

        

        return [
            'projectOverdue'         => $overdue['count'],
            'projectDueThisMonth'    => $dueThisMonth['count'],
            'projectOnTrack'         => $onTrack['count'],
            'totalActiveProjects'    => Project::count(),
            'totalWorkOrders'        => WorkOrder::whereMonth('wo_date', $now->month)->whereYear('wo_date', $now->year)->where('status', 'finished')->count(),
        ];
    }

    private function getProjectsByCriteria(Carbon $now, callable $phcFilter, bool $excludeFinished = false)
    {
        $query = Project::whereHas('phc', $phcFilter);

        if ($excludeFinished) {
            $query->whereNull('engineering_finish_date');
        }

        $count = $query->count();

        $listQuery = clone $query;
        $list = $listQuery->with(['statusProject', 'phc'])
            ->get()
            ->map(function ($p) {
                return [
                    'pn_number'    => $p->pn_number,
                    'project_name' => $p->project_name,
                    'target_dates' => $p->phc->target_finish_date,
                    'status'       => $p->statusProject->name ?? '-',
                    'pic'          => $p->phc?->picEngineering?->name ?? '-',
                ];
            });

        return [
            'count' => $count,
            'list'  => $list,
        ];
    }

    private function getCharts(Carbon $now)
    {
        // Line Chart: Completed projects per month
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

        return [
            'months'             => $months->pluck('label'),
            'completedProjects'  => $months->pluck('value'),
            'statusLabels'       => $statusLabels->values(),
            'statusCounts'       => $statusLabels->keys()->map(fn ($id) => $statusCounts[$id] ?? 0),
        ];
    }

    private function getProjectLists(Carbon $now)
    {
        // Upcoming Projects
        $upcomingProjects = Project::whereHas('phc', function($q) use ($now) {
                $q->whereBetween('target_finish_date', [$now, $now->copy()->addDays(30)]);
            })
            ->with(['statusProject', 'phc'])
            ->get()
            ->sortBy('phc.target_finish_date')
            ->map(function ($p) {
                return [
                    'pn_number'    => $p->pn_number,
                    'project_name' => $p->project_name,
                    'target_dates' => $p->phc->target_finish_date,
                    'status'       => $p->statusProject->name ?? '-',
                ];
            });

        $projectDueThisMonthList = Project::whereHas('phc', function($q) use ($now) {
            $q->whereNotNull('target_finish_date')
                ->whereMonth('target_finish_date', $now->month)
                ->whereYear('target_finish_date', $now->year);
        })
        ->with(['statusProject', 'phc'])
        ->get()
        ->map(function ($p) {
            return [
                'pn_number'    => $p->pn_number,
                'project_name' => $p->project_name,
                'target_dates' => $p->phc->target_finish_date,
                'status'       => $p->statusProject->name ?? '-',
                'pic'          => $p->phc?->picEngineering?->name ?? '-',
            ];
        });

        $projectOnTrackList = Project::whereHas('phc', function($q) use ($now) {
                $q->whereNotNull('target_finish_date')
                  ->where('target_finish_date', '>=', $now);
            })
            ->whereNull('engineering_finish_date')
            ->with(['statusProject', 'phc'])
            ->get()
            ->map(function ($p) {
                return [
                    'pn_number'    => $p->pn_number,
                    'project_name' => $p->project_name,
                    'target_dates' => $p->phc->target_finish_date,
                    'status'       => $p->statusProject->name ?? '-',
                    'pic'          => $p->phc?->picEngineering?->name ?? '-',
                ];
            });

        // Top 5 Overdue Projects
        $top5Overdue = Project::whereHas('phc', function($q) use ($now) {
                $q->whereNotNull('target_finish_date')
                  ->where('target_finish_date', '<', $now);
            })
            ->whereNull('engineering_finish_date')
            ->with([
                'statusProject',
                'phc.picEngineering',
            ])
            ->get()
            ->map(function ($p) use ($now) {
                return [
                    'pn_number'    => $p->pn_number,
                    'project_name' => $p->project_name,
                    'target_dates' => $p->phc->target_finish_date,
                    'delay_days'   => Carbon::parse($p->phc->target_finish_date)->diffInDays($now),
                    'status'       => $p->statusProject->name ?? '-',
                    'pic'          => $p->phc?->picEngineering?->name ?? '-',
                ];
            })
            ->sortByDesc('delay_days')
            ->values();

        return [
            'upcomingProjects' => $upcomingProjects,
            'projectDueThisMonthList' => $projectDueThisMonthList,
            'top5Overdue'      => $top5Overdue,
            'projectOnTrackList' => $projectOnTrackList,
        ];
    }

    private function getWorkOrdersThisMonth()
    {
        return [
            'workOrdersThisMonth' => WorkOrder::whereMonth('wo_date', now()->month)
                ->whereYear('wo_date', now()->year)
                ->with(['pics.user', 'descriptions', 'project.client'])
                ->get()
                ->map(function ($wo) {
                    return [
                        'wo_kode_no' => $wo->wo_kode_no,
                        'project_name' => $wo->project->project_name ?? '-',
                        'client_name' => $wo->project->client->name ?? $wo->project->quotation->client->name,
                        'created_by' => $wo->creator?->name ?? '-',
                        'pic_names' => $wo->pics->pluck('user.name')->join(', '),
                        'descriptions' => $wo->descriptions->pluck('description')->join('; '),
                        'results' => $wo->descriptions->pluck('result')->join('; '),
                        'wo_date' => $wo->wo_date,
                    ];
                }),
        ];
    }
}
