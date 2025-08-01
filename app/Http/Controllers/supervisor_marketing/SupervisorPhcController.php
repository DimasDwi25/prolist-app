<?php

namespace App\Http\Controllers\supervisor_marketing;

use App\Events\PhcCreatedEvent;
use App\Http\Controllers\Controller;
use App\Models\PHC;
use App\Models\PhcApproval;
use App\Models\Project;
use App\Models\User;
use App\Notifications\PhcAllApprovedNotification;
use App\Notifications\PhcValidationRequested;
use Auth;
use Illuminate\Http\Request;

class SupervisorPhcController extends Controller
{
    //
    public function create($project_id)
    {
        $project = Project::with('quotation', 'phc')->findOrFail($project_id);

        // Jika PHC sudah ada, redirect ke edit
        if ($project->phc) {
            return redirect()->route('phc.edit', $project->phc->id)
                ->with('info', 'PHC sudah dibuat, silakan edit.');
        }

        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', [
                'engineer',
                'supervisor engineer',
                'project manager',
                'project controller',
                'admin engineer',
                'supervisor marketing',
                'administration marketing',
            ]);
        })->with('role')->get();


        $phc = null;

        return view('supervisor.phcs.form', compact('project', 'users', 'phc'));
    }

    private function mapToBoolean(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            // Default NA if not set
            $value = $data[$field] ?? 'NA';
            $data[$field] = $value === 'A' ? true : false;
        }
        return $data;
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'handover_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'target_finish_date' => 'nullable|date',
            'client_name' => 'nullable|string',
            'client_mobile' => 'nullable|string',
            'client_reps_office_address' => 'nullable|string',
            'client_site_representatives' => 'nullable|string',
            'client_site_address' => 'nullable|string',
            'site_phone_number' => 'nullable|string',
            'marketing_pic_id' => 'nullable|exists:users,id',
            'pic_engineering_id' => 'nullable|exists:users,id',
            'ho_marketings_id' => 'nullable|exists:users,id',
            'ho_engineering_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'status' => 'string',
            'costing_by_marketing' => 'nullable|in:A,NA',
            'boq' => 'nullable|in:A,NA',
            'retention' => 'nullable|string',
            'warranty' => 'nullable|in:A,NA',
            'penalty' => 'nullable|in:A,NA',
            'scope_of_work_approval' => 'nullable|in:A,NA',
            'organization_chart' => 'nullable|in:A,NA',
            'project_schedule' => 'nullable|in:A,NA',
            'component_list' => 'nullable|in:A,NA',
            'progress_claim_report' => 'nullable|in:A,NA',
            'component_approval_list' => 'nullable|in:A,NA',
            'design_approval_draw' => 'nullable|in:A,NA',
            'shop_draw' => 'nullable|in:A,NA',
            'fat_sat_forms' => 'nullable|in:A,NA',
            'daily_weekly_progress_report' => 'nullable|in:A,NA',
            'do_packing_list' => 'nullable|in:A,NA',
            'site_testing' => 'nullable|in:A,NA',
            'site_testing_commissioning_report' => 'nullable|in:A,NA',
            'as_build_draw' => 'nullable|in:A,NA',
            'manual_documentation' => 'nullable|in:A,NA',
            'accomplishment_report' => 'nullable|in:A,NA',
            'client_document_requirements' => 'nullable|in:A,NA',
            'job_safety_analysis' => 'nullable|in:A,NA',
            'risk_assessment' => 'nullable|in:A,NA',
            'tool_list' => 'nullable|in:A,NA',

            // Add validation rules for checklist fields as needed
        ]);

        $checklistFields = [
            'costing_by_marketing',
            'boq',
            'retention_applicability',
            'retention',
            'warranty',
            'warranty_detail',
            'penalty',
            'penalty_detail',
            'scope_of_work_approval',
            'organization_chart',
            'project_schedule',
            'component_list',
            'progress_claim_report',
            'component_approval_list',
            'design_approval_draw',
            'shop_draw',
            'fat_sat_forms',
            'daily_weekly_progress_report',
            'do_packing_list',
            'site_testing',
            'site_testing_commissioning_report',
            'as_build_draw',
            'manual_documentation',
            'accomplishment_report',
            'client_document_requirements',
            'job_safety_analysis',
            'risk_assessment',
            'tool_list',
        ];


        $booleanData = $this->mapToBoolean($request->all(), $checklistFields);
        $validated = array_merge($validated, $booleanData);
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending'; // default

        $phc = Phc::create($validated);

        // Ambil 4 user dari field input PHC
        $userIds = array_filter([
            $request->ho_engineering_id,
            $request->ho_marketings_id,
            $request->pic_engineering_id,
            $request->pic_marketing_id,
        ]);

        foreach ($userIds as $id) {
            PhcApproval::create([
                'phc_id' => $phc->id,
                'user_id' => $id,
                'status' => 'pending',
            ]);
        }

        // Broadcast ke public channel
        event(new PhcCreatedEvent($phc, $userIds));


        session()->flash('resetStep', true);

        return redirect()->route('supervisor.project.show', $request->project_id)
            ->with('success', 'PHC created successfully and waiting for approval.');
    }

    public function edit(PHC $phc)
    {
        $project = $phc->project()->with('quotation')->first();
        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['supervisor marketing', 'administration marketing', 'engineer']);
        })->get();

        return view('supervisor.phcs.form', compact('phc', 'project', 'users'));
    }

    public function update(Request $request, PHC $phc)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'handover_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'target_finish_date' => 'nullable|date',
            'client_name' => 'nullable|string',
            'client_mobile' => 'nullable|string',
            'client_reps_office_address' => 'nullable|string',
            'client_site_representatives' => 'nullable|string',
            'client_site_address' => 'nullable|string',
            'site_phone_number' => 'nullable|string',
            'marketing_pic_id' => 'nullable|exists:users,id',
            'pic_engineering_id' => 'nullable|exists:users,id',
            'ho_marketings_id' => 'nullable|exists:users,id',
            'ho_engineering_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'costing_by_marketing' => 'nullable|in:A,NA',
            'boq' => 'nullable|in:A,NA',
            'retention' => 'nullable|string',
            'warranty' => 'nullable|in:A,NA',
            'penalty' => 'nullable|in:A,NA',
            'scope_of_work_approval' => 'nullable|in:A,NA',
            'organization_chart' => 'nullable|in:A,NA',
            'project_schedule' => 'nullable|in:A,NA',
            'component_list' => 'nullable|in:A,NA',
            'progress_claim_report' => 'nullable|in:A,NA',
            'component_approval_list' => 'nullable|in:A,NA',
            'design_approval_draw' => 'nullable|in:A,NA',
            'shop_draw' => 'nullable|in:A,NA',
            'fat_sat_forms' => 'nullable|in:A,NA',
            'daily_weekly_progress_report' => 'nullable|in:A,NA',
            'do_packing_list' => 'nullable|in:A,NA',
            'site_testing' => 'nullable|in:A,NA',
            'site_testing_commissioning_report' => 'nullable|in:A,NA',
            'as_build_draw' => 'nullable|in:A,NA',
            'manual_documentation' => 'nullable|in:A,NA',
            'accomplishment_report' => 'nullable|in:A,NA',
            'client_document_requirements' => 'nullable|in:A,NA',
            'job_safety_analysis' => 'nullable|in:A,NA',
            'risk_assessment' => 'nullable|in:A,NA',
            'tool_list' => 'nullable|in:A,NA',
            // Add checklist fields
        ]);

        $checklistFields = [
            'costing_by_marketing',
            'boq',
            'retention_applicability',
            'retention',
            'warranty',
            'warranty_detail',
            'penalty',
            'penalty_detail',
            'scope_of_work_approval',
            'organization_chart',
            'project_schedule',
            'component_list',
            'progress_claim_report',
            'component_approval_list',
            'design_approval_draw',
            'shop_draw',
            'fat_sat_forms',
            'daily_weekly_progress_report',
            'do_packing_list',
            'site_testing',
            'site_testing_commissioning_report',
            'as_build_draw',
            'manual_documentation',
            'accomplishment_report',
            'client_document_requirements',
            'job_safety_analysis',
            'risk_assessment',
            'tool_list',
        ];

        $booleanData = $this->mapToBoolean($request->all(), $checklistFields);
        $validated = array_merge($validated, $booleanData);

        $phc->update($validated);

        session()->flash('resetStep', true);

        return redirect()->route('supervisor.project.show', $phc->project_id)->with('success', 'PHC updated successfully.');

    }

    public function show(PHC $phc)
    {
        $phc->load('project.quotation');
        return view('supervisor.phcs.show', compact('phc'));
    }
}
