<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\CategorieProject;
use App\Models\Project;
use App\Models\Quotation;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    //
    public function index()
    {
        $projects = Project::with(['category', 'quotation'])->get();
        return view('marketing.project.index', compact('projects'));
    }

    public function create()
    {
        $categories = CategorieProject::all();
        $quotations = Quotation::all();
        return view('marketing.project.form', compact('categories', 'quotations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'categories_project_id' => 'nullable|exists:project_categories,id',
            'quotations_id' => 'nullable|exists:quotations,id',
            'phc_dates' => 'nullable|date',
            'mandays_engineer' => 'nullable|integer',
            'mandays_technician' => 'nullable|integer',
            'target_dates' => 'nullable|date',
            'material_status' => 'nullable|string',
            'dokumen_finish_date' => 'nullable|date',
            'engineering_finish_date' => 'nullable|date',
            'jumlah_invoice' => 'nullable|integer',
            'status_project_id' => 'nullable|exists:status_projects,id',
            'project_progress' => 'nullable|integer|min:0|max:100',
        ]);

        Project::create($validated);

        return redirect()->route('marketing.project')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        $categories = CategorieProject::all();
        $quotations = Quotation::all();
        return view('marketing.project.form', compact('project', 'categories', 'quotations'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'categories_project_id' => 'nullable|exists:categorie_projects,id',
            'quotations_id' => 'nullable|exists:quotations,id',
            'phc_dates' => 'nullable|date',
            'mandays_engineer' => 'nullable|integer',
            'mandays_technician' => 'nullable|integer',
            'target_dates' => 'nullable|date',
            'material_status' => 'nullable|string',
            'dokumen_finish_date' => 'nullable|date',
            'engineering_finish_date' => 'nullable|date',
            'jumlah_invoice' => 'nullable|integer',
            'status_project_id' => 'nullable|exists:status_projects,id',
            'project_progress' => 'nullable|integer|min:0|max:100',
        ]);

        $project->update($validated);

        return redirect()->route('marketing.project')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('marketing.project')->with('success', 'Project deleted.');
    }

    public function show(Project $project)
    {
        $project->load(['phc', 'quotation']); // pastikan eager loading PHC
        return view('marketing.project.show', compact('project'));
    }

}
