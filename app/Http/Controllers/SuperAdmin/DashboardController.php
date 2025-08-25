<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Department;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        return view('admin.dashboard', [
            'totalUsers'      => User::count(),
            'totalRoles'      => Role::count(), // kalau role di table 'roles'
            'totalDepartments'=> Department::count(),
            'totalClients'    => Client::count(),
            'projects'        => [
                'On Progress'   => Project::whereHas('statusProject', fn($q) => $q->where('name', 'On Progress'))->count(),
                'Documents Completed' => Project::whereHas('statusProject', fn($q) => $q->where('name', 'Documents Completed'))->count(),
                'Engineering Work Completed' => Project::whereHas('statusProject', fn($q) => $q->where('name', 'Engineering Work Completed'))->count(),
                'Hold By Customer' => Project::whereHas('statusProject', fn($q) => $q->where('name', 'Hold By Customer'))->count(),
                'Project Finished' => Project::whereHas('statusProject', fn($q) => $q->where('name', 'Project Finished'))->count(),
                'Material Delay' => Project::whereHas('statusProject', fn($q) => $q->where('name', 'Material Delay'))->count(),
                'Invoice On Progress' => Project::whereHas('statusProject', fn($q) => $q->where('name', 'Invoice On Progress'))->count(),

                'total'     => Project::count(),
            ]
        ]);
    }
}
