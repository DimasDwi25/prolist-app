<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectLogController extends Controller
{
    //
    public function show($id)
    {
        $project = Project::with('quotation')->findOrFail($id);

        return view('marketing.project.logs', compact('project'));
    }
}
