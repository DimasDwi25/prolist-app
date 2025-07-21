<?php

namespace App\Http\Controllers\ProjectController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectControllerDashboardController extends Controller
{
    //
    public function index()
    {
        return view('project-controller.dashboard');
    }
}
