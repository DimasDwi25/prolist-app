<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class EngineerProjectController extends Controller
{
    //
    public function index()
    {
        $projects = Project::with(['category', 'quotation'])->get();
        return view('engineer.project.index', compact('projects'));
        
    }

    public function show(Project $project)
    {
        $project->load(['phc', 'quotation']);

        // dd($project);
        return view('engineer.project.show', compact('project'));
    }
}
