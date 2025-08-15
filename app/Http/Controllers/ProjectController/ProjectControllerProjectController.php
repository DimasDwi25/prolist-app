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
        // Eager load all necessary relationships
        $project->load([
            'variants.statusProject',
            'parent.statusProject',
            'statusProject',
            'category',
            'phc.approvals.user'
        ]);
        
        // Get pending approvals with null safety
        $phc = $project->phc;
        $pendingApprovals = $phc
            ? $phc->approvals()->where('status', 'pending')->with('user')->get()
            : collect();

        // Calculate relationship flags
        $hasParent = !is_null($project->parent);
        $hasVariants = $project->variants->isNotEmpty();

        return view('project-controller.project.show', [
            'project' => $project,
            'phc' => $phc, // Pass the PHC explicitly
            'pendingApprovals' => $pendingApprovals,
            'parentProject' => $project->parent,
            'childProjects' => $project->variants,
            'hasParent' => $hasParent,
            'hasVariants' => $hasVariants,
            'display' => fn($value) => $value ?: '—',
            'formatDate' => fn($date) => $date ? \Carbon\Carbon::parse($date)->translatedFormat('d M Y') : '—',
            'formatDecimal' => fn($decimal) => is_numeric($decimal) ? number_format($decimal, 0, ',', '.') : '—'
        ]);
    }
}
