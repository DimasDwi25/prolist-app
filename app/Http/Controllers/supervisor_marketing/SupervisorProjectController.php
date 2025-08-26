<?php

namespace App\Http\Controllers\supervisor_marketing;

use App\Http\Controllers\Controller;
use App\Models\CategorieProject;
use App\Models\Client;
use App\Models\Project;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupervisorProjectController extends Controller
{
    //
    public function index()
    {
        $projects = Project::with(['category', 'quotation'])->get();
        return view('supervisor.project.index', compact('projects'));
    }

    public function create()
    {
        $categories = CategorieProject::all();
        $quotations = Quotation::with('client')->whereIn('status', ['A', 'D'])->get(); // Only show active or ongoing quotations
        $projects = Project::whereNull('parent_pn_number')
                    ->orderBy('pn_number', 'asc')
                    ->get();
        $clients = Client::all();
        $project = new Project([
            'status_project_id' => 1, // Default status
            'is_confirmation_order' => false,
            'is_variant_order' => false
        ]);
        return view('supervisor.project.form', compact('categories', 'quotations', 'projects', 'clients', 'project'));
    }

    public function store(Request $request)
    {
         Log::debug('Store method reached'); 
         Log::debug('Method:', ['method' => $request->method()]); // Harusnya POST
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'categories_project_id' => 'nullable|exists:project_categories,id',
            'quotations_id' => 'required|exists:quotations,quotation_number',
            'phc_dates' => 'nullable|date',
            'mandays_engineer' => 'nullable|integer',
            'mandays_technician' => 'nullable|integer',
            'target_dates' => 'nullable|date',
            'status_project_id' => 'nullable|exists:status_projects,id',
            // Kolom baru
            'po_date' => 'nullable|date',
            'sales_weeks' => 'nullable|string|max:50',
            'po_number' => 'nullable|string|max:100',
            'po_value' => 'nullable|numeric',
            'is_confirmation_order' => 'nullable|boolean',
            'parent_pn_number' => 'nullable|exists:projects,pn_number',
            'client_id' => 'nullable|exists:clients,id',
           
        ]);

        
        // Set default false jika checkbox tidak dicentang
        $validated['is_confirmation_order'] = $request->has('is_confirmation_order');

        Project::create($validated);

        return redirect()->route('supervisor.project')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        $categories = CategorieProject::all();
        $quotations = Quotation::with('client')->whereIn('status', ['A', 'D'])->get(); // Only show active or ongoing quotations
        $projects = Project::whereNull('parent_pn_number')
                    ->orderBy('pn_number', 'asc')
                    ->get();
        $clients = Client::all();
        return view('supervisor.project.form', compact('project','categories', 'quotations', 'projects', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'categories_project_id' => 'nullable|exists:project_categories,id',
            'quotations_id' => 'nullable|exists:quotations,quotation_number',
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

            // Kolom baru
            'po_date' => 'nullable|date',
            'sales_weeks' => 'nullable|string|max:50',
            'po_number' => 'nullable|string|max:100',
            'po_value' => 'nullable|numeric',
            'is_confirmation_order' => 'nullable|boolean',
            'parent_pn_number' => 'nullable|exists:projects,pn_number',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        $validated['is_confirmation_order'] = $request->has('is_confirmation_order');

        $project->update($validated);

        return redirect()->route('supervisor.project')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        // Hapus approval
        \App\Models\PhcApproval::whereIn('phc_id', $project->phcs()->pluck('id'))->delete();

        // Hapus PHC yang terkait
        $project->phcs()->delete();

        // Hapus project
        $project->delete();

        return back()->with('success', 'Project deleted successfully');
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

        return view('supervisor.project.show', [
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

    public function generateProjectNumber(Request $request)
    {
        $isCO = $request->input('is_co', false);
        $currentProject = $request->input('current_project', null);
        
        // Jika sedang edit dan sudah ada project number, pertahankan
        if ($currentProject && !$isCO) {
            return response()->json(['project_number' => $currentProject]);
        }
        
        // Generate PN number baru
        $pnNumber = Project::generatePnNumber();
        
        // Generate project number berdasarkan tipe (CO atau regular)
        $projectNumber = Project::generateProjectNumber($pnNumber, $isCO);
        
        return response()->json(['project_number' => $projectNumber]);
    }
}
