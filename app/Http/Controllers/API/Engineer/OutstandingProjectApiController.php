<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\ManPowerAllocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OutstandingProjectApiController extends Controller
{
    private const ALLOWED_ROLES = [
        'project manager',
        'engineer_supervisor',
        'engineer',
        'electrician_supervisor',
        'electrician',
        'drafter',
        'site_engineer'
    ];

    private const EXCLUDED_STATUSES = [
        'Engineering Work Completed',
        'Project Finished',
        'Invoice On Progress',
        'Documents Completed',
        'Cancelled'
    ];

    public function index()
    {
        $users = $this->buildUserQuery()->get();
        $result = $users->map(fn($user) => $this->mapUser($user));

        return response()->json($result);
    }

    private function buildUserQuery()
    {
        return User::with([
            'manPowerAllocations.project' => function ($q) {
                $q->whereHas('statusProject', function ($q2) {
                    $q2->whereNotIn('name', self::EXCLUDED_STATUSES);
                })
                ->with(['client', 'quotation.client']);
            },
            'manPowerAllocations.project.statusProject:id,name',
            'manPowerAllocations.project.phc:project_id,target_finish_date',
            'manPowerAllocations.project.logs' => function ($q) {
                $q->select('id', 'project_id', 'users_id', 'tgl_logs', 'logs')
                  ->latest('tgl_logs')
                  ->with('user:id,name');
            },
        ])
        ->whereHas('role', function ($q) {
            $q->whereIn('name', self::ALLOWED_ROLES);
        });
    }

    private function mapUser($user)
    {
        return [
            'user_id' => $user->id,
            'photo' => $this->getPhotoUrl($user),
            'pic' => $user->name,
            'projects' => $user->manPowerAllocations
                ->map(fn($allocation) => $this->mapProject($allocation, $user->id))
                ->filter()
                ->values(),
        ];
    }

    private function getPhotoUrl($user)
    {
        if (!$user->photo) {
            return null;
        }

        return Storage::disk('public')->exists($user->photo)
            ? Storage::disk('public')->url($user->photo)
            : asset('storage/' . ltrim($user->photo, '/'));
    }

    private function mapProject($allocation, $userId)
    {
        $project = $allocation->project;

        if (!$project) {
            return null;
        }

        $client = $project->client ?? $project->quotation->client ?? null;

        return [
            'project_number' => $project->project_number ?? '-',
            'project_name' => $project->project_name ?? '-',
            'client' => $client ? $this->mapClient($client) : null,
            'target_date' => optional($project->phc)->target_finish_date,
            'status' => $project->statusProject->name ?? '-',
            'logs' => $project->logs->where('users_id', $userId)->take(3)->map(fn($log) => $this->mapLog($log))->toArray(),
        ];
    }

    private function mapClient($client)
    {
        return [
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
        ];
    }

    private function mapLog($log)
    {
        return [
            'user' => $log->user->name ?? 'Unknown',
            'tgl' => $log->tgl_logs ? \Carbon\Carbon::parse($log->tgl_logs)->format('Y-m-d H:i') : null,
            'log' => $log->logs,
        ];
    }
}
