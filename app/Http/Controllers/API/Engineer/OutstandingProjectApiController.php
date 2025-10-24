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
            'site_engineer'
        ];

        // Ambil semua user sesuai role + relasi allocations & project
        $users = User::with([
            'manPowerAllocations.project' => function ($q) {
                $q->with(['client', 'quotation.client', 'logs' => function ($q) {
                    $q->select('id', 'project_id', 'users_id', 'tgl_logs', 'logs') // wajib bawa project_id karena fk → pn_number
                    ->orderBy('tgl_logs', 'desc')
                    ->take(3)
                    ->with('user:id,name');
                }]);
            },
            'manPowerAllocations.project.statusProject:status_projects.id,name',
            'manPowerAllocations.project.phc:project_id,target_finish_date',

        ])
        ->whereHas('role', function ($q) use ($allowedRoles) {
            $q->whereIn('name', $allowedRoles);
        })
        ->get();

        // Mapping hasil
        $result = $users->map(function ($user) {
            return [
                'user_id' => $user->id,
                'photo' => $user->photo
                ? (Storage::disk('public')->exists($user->photo)
                    ? Storage::disk('public')->url($user->photo)
                    : asset('storage/' . ltrim($user->photo, '/')))
                : null,

                'pic' => $user->name,
                'projects' => $user->manPowerAllocations->map(function ($allocation) {
                    $project = $allocation->project;

                    if (!$project) {
                        return null; // lewati jika project tidak ditemukan
                    }

                    // Filter projects by status: exclude completed/cancelled ones
                    $excludedStatuses = [
                        'Engineering Work Completed',
                        'Project Finished',
                        'Invoice On Progress',
                        'Documents Completed',
                        'Cancelled'
                    ];
                    if (in_array($project->statusProject->name ?? '', $excludedStatuses)) {
                        return null; // skip excluded statuses
                    }

                    $client = $project->client ?? $project->quotation->client ?? null;

                    return [
                        'project_number' => $project->project_number ?? '-',
                        'project_name'   => $project->project_name ?? '-',
                        'client'         => $client ? [
                            'name' => $client->name ?? '-',
                            'address' => $client->address ?? '-',
                            'phone' => $client->phone ?? '-',
                            'client_representative' => $client->client_representative ?? '-',
                            'city' => $client->city ?? '-',
                            'province' => $client->province ?? '-',
                            'country' => $client->country ?? '-',
                            'zip_code' => $client->zip_code ?? '-',
                            'web' => $client->web ?? '-',
                            'notes' => $client->notes ?? '-',
                        ] : null,
                        'target_date'    => optional($project->phc)->target_finish_date,
                        'status'         => $project->statusProject->name ?? '-',
                        'logs'           => $project->logs->map(function ($log) {
                            return [
                                'user' => $log->user->name ?? 'Unknown',
                                'tgl'  => $log->tgl_logs ? \Carbon\Carbon::parse($log->tgl_logs)->format('Y-m-d H:i') : null,
                                'log'  => $log->logs,
                            ];
                        }),
                    ];
                })->filter()->values(), // filter() untuk buang null result

            ];
        });

        return response()->json($result);
    }
}
