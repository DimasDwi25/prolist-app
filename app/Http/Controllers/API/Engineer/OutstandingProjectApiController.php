<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\ManPowerAllocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OutstandingProjectApiController extends Controller
{
    //
     public function index()
    {
        // Role yang diizinkan
        $allowedRoles = [
            'project manager',
            'engineer_supervisor',
            'engineer',
            'electrician_supervisor',
            'electrician',
            'drafter',
        ];

        // Ambil semua user sesuai role + relasi allocations & project
        $users = User::with([
            'manPowerAllocations.project' => function ($q) {
                $q->select('pn_number', 'project_name', 'status_project_id', 'project_number') // pk = pn_number
                ->whereHas('statusProject', function ($q2) {
                    $q2->where('name', '!=', 'Project Finished');
                });
            },
            'manPowerAllocations.project.statusProject:status_projects.id,name',
            'manPowerAllocations.project.phc:project_id,target_finish_date',
            'manPowerAllocations.project.logs' => function ($q) {
                $q->select('id', 'project_id', 'users_id', 'tgl_logs', 'logs') // wajib bawa project_id karena fk → pn_number
                ->latest('tgl_logs')
                ->take(3)
                ->with('user:id,name');
            },

        ])
        ->whereHas('role', function ($q) use ($allowedRoles) {
            $q->whereIn('name', $allowedRoles);
        })
        ->get();

        // Mapping hasil
        $result = $users->map(function ($user) {
            return [
                'user_id' => $user->id,
                'photo' => $user->photo ? Storage::url($user->photo) : null,
                'pic' => $user->name,
                'projects' => $user->manPowerAllocations->map(function ($allocation) {
                    return [
                        'project_number' => $allocation->project->project_number,
                        'project_name'   => $allocation->project->project_name,
                        'target_date'    => optional($allocation->project->phc)->target_finish_date,
                        'status'         => $allocation->project->statusProject->name ?? '-',
                        'logs'           => $allocation->project->logs->map(function ($log) {
                            return [
                                'user' => $log->user->name ?? 'Unknown',
                                'tgl'  => $log->tgl_logs?->format('Y-m-d H:i'),
                                'log'  => $log->logs,
                            ];
                        }),
                    ];
                })->values(),
            ];
        });

        return response()->json($result);
    }
}
