<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\StatusProject;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManPowerDashboardApiController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->name ?? null;
        $userId = $user->id;

        $allowedRoles = [
            'engineer_supervisor',
            'engineer',
            'drafter',
            'electrician_supervisor',
            'electrician',
        ];

        // Kalau rolenya tidak termasuk, return forbidden
        if (!in_array($role, $allowedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized role',
            ], 403);
        }

        $now = Carbon::now();

        $kpis = $this->getKpis($now, $userId, $role);
        $workOrders = $this->getWorkOrdersThisMonth($userId, $role);
        $projects = $this->getProjectLists($now, $userId, $role);

        return response()->json(array_merge($kpis, $projects, $workOrders));
    }

    private function getKpis(Carbon $now, $userId, $role)
    {
        $overdue = $this->getProjectsByCriteria($now, $userId, $role, function($q) use ($now) {
            $q->whereNotNull('target_finish_date')->where('target_finish_date', '<', $now);
        }, true);

        $dueThisMonth = $this->getProjectsByCriteria($now, $userId, $role, function($q) use ($now) {
            $q->whereNotNull('target_finish_date')
              ->whereMonth('target_finish_date', $now->month)
              ->whereYear('target_finish_date', $now->year);
        }, true);

        $onTrack = $this->getProjectsByCriteria($now, $userId, $role, function($q) use ($now) {
            $q->whereNotNull('target_finish_date')->where('target_finish_date', '>=', $now);
        }, true);

        $totalActiveProjects = $this->getUserProjectsQuery($userId, $role)->count();

        $totalWorkOrders = WorkOrder::whereMonth('wo_date', $now->month)
            ->whereYear('wo_date', $now->year)
            ->where('status', 'finished')
            ->whereHas('project', function($q) use ($userId, $role) {
                $this->applyUserProjectFilter($q, $userId, $role);
            })
            ->count();

        return [
            'projectOverdue'         => $overdue['count'],
            'projectDueThisMonth'    => $dueThisMonth['count'],
            'projectOnTrack'         => $onTrack['count'],
            'totalActiveProjects'    => $totalActiveProjects,
            'totalWorkOrders'        => $totalWorkOrders,
        ];
    }

    private function getProjectsByCriteria(Carbon $now, $userId, $role, callable $phcFilter, bool $excludeFinished = false)
    {
        $query = $this->getUserProjectsQuery($userId, $role)->whereHas('phc', $phcFilter);

        if ($excludeFinished) {
            $query->whereHas('statusProject', function($q) {
                $q->whereNotIn('name', ['Engineering Work Completed', 'Project Finished', 'Invoice On Progress', 'Documents Completed', 'Cancelled']);
            });
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

    private function getProjectLists(Carbon $now, $userId, $role)
    {
        // Upcoming Projects
        $upcomingProjects = $this->getUserProjectsQuery($userId, $role)
            ->whereHas('phc', function($q) use ($now) {
                $q->whereBetween('target_finish_date', [$now, $now->copy()->addDays(30)]);
            })
            ->whereHas('statusProject', function($q) {
                $q->whereNotIn('name', ['Engineering Work Completed', 'Project Finished', 'Invoice On Progress', 'Documents Completed', 'Cancelled']);
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

        $projectDueThisMonthList = $this->getUserProjectsQuery($userId, $role)
            ->whereHas('phc', function($q) use ($now) {
                $q->whereNotNull('target_finish_date')
                    ->whereMonth('target_finish_date', $now->month)
                    ->whereYear('target_finish_date', $now->year);
            })
            ->whereHas('statusProject', function($q) {
                $q->whereNotIn('name', ['Engineering Work Completed', 'Project Finished', 'Invoice On Progress', 'Documents Completed', 'Cancelled']);
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

        $projectOnTrackList = $this->getUserProjectsQuery($userId, $role)
            ->whereHas('phc', function($q) use ($now) {
                $q->whereNotNull('target_finish_date')
                  ->where('target_finish_date', '>=', $now);
            })
            ->whereHas('statusProject', function($q) {
                $q->whereNotIn('name', ['Engineering Work Completed', 'Project Finished', 'Invoice On Progress', 'Documents Completed', 'Cancelled']);
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

        // Top 5 Overdue Projects
        $top5Overdue = $this->getUserProjectsQuery($userId, $role)
            ->whereHas('phc', function($q) use ($now) {
                $q->whereNotNull('target_finish_date')
                  ->where('target_finish_date', '<', $now);
            })
            ->whereHas('statusProject', function($q) {
                $q->whereNotIn('name', ['Engineering Work Completed', 'Project Finished', 'Invoice On Progress', 'Documents Completed', 'Cancelled']);
            })
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
            ->take(5)
            ->values();

        return [
            'upcomingProjects' => $upcomingProjects,
            'projectDueThisMonthList' => $projectDueThisMonthList,
            'top5Overdue'      => $top5Overdue,
            'projectOnTrackList' => $projectOnTrackList,
        ];
    }

    private function getWorkOrdersThisMonth($userId, $role)
    {
        return [
            'workOrdersThisMonth' => WorkOrder::whereMonth('wo_date', now()->month)
                ->whereYear('wo_date', now()->year)
                ->whereHas('project', function($q) use ($userId, $role) {
                    $this->applyUserProjectFilter($q, $userId, $role);
                })
                ->where('status', 'finished')
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

    private function getUserProjectsQuery($userId, $role)
    {
        return Project::query()
            ->where(function ($query) use ($userId, $role) {
                $query->whereHas('manPowerAllocations', function ($sub) use ($userId) {
                    $sub->where('user_id', $userId);
                })
                ->orWhereHas('phc', function ($sub) use ($userId, $role) {
                    if (in_array($role, ['engineer', 'engineer_supervisor'])) {
                        $sub->where('pic_engineering_id', $userId)
                            ->orWhere('ho_engineering_id', $userId);
                    }

                    if ($role === 'drafter') {
                        $sub->where('drafter_id', $userId);
                    }

                    if (in_array($role, ['electrician', 'electrician_supervisor'])) {
                        $sub->where('pic_electrician_id', $userId)
                            ->orWhere('ho_electrician_id', $userId);
                    }
                });
            });
    }


    private function applyUserProjectFilter($query, $userId, $role)
    {
        $query->where(function ($q) use ($userId) {
            $q->whereHas('manPowerAllocations', function ($sub) use ($userId) {
                $sub->where('user_id', $userId);
            });
        })
        ->orWhere(function ($q) use ($userId, $role) {
            $q->whereHas('phc', function ($sub) use ($userId, $role) {
                if (in_array($role, ['engineer', 'engineer_supervisor'])) {
                    $sub->where('pic_engineering_id', $userId)
                        ->orWhere('ho_engineering_id', $userId);
                }

                if ($role === 'drafter') {
                    $sub->where('drafter_id', $userId);
                }

                if (in_array($role, ['electrician', 'electrician_supervisor'])) {
                    $sub->where('pic_electrician_id', $userId)
                        ->orWhere('ho_electrician_id', $userId);
                }
            });
        });
    }
}
