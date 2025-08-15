<?php

namespace App\Http\Controllers\ProjectController;

use App\Http\Controllers\Controller;
use App\Models\PHC;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectControllerPhcController extends Controller
{
    //
    public function show(PHC $phc)
    {
        $phc->load('project.quotation');
        return view('project-controller.phcs.show', compact('phc'));
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

    public function edit(PHC $phc)
    {
        $project = $phc->project()->with('quotation')->first();
        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['supervisor marketing', 'engineer', 'project manager', 'super_admin']);
        })->get();

        return view('project-controller.phcs.form', compact('phc', 'project', 'users'));
    }

    public function update(Request $request, PHC $phc)
    {
        $validated = $request->validate([
            'pic_engineering_id' => 'nullable|exists:users,id',
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

        return redirect()->route('project_controller.project.show', $phc->project_id)->with('success', 'PHC updated successfully.');

    }
}
