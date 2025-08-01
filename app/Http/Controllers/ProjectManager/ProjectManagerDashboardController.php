<?php

namespace App\Http\Controllers\ProjectManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectManagerDashboardController extends Controller
{
    //
    public function index()
    {
        return view('project-manager.dashboard');
    }
}
