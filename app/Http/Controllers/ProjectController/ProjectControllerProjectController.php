<?php

namespace App\Http\Controllers\ProjectController;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectControllerProjectController extends Controller
{
    //
    public function index()
    {
        $projects = Project::with(['category', 'quotation'])->get();
        return view('project-controller.project.index', compact('projects'));
        
    }

    public function show(Project $project)
    {
        $project->load(['phc', 'quotation']);

        // dd($project);
        return view('project-controller.project.show', compact('project'));
    }
}
