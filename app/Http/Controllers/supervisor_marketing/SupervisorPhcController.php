<?php

namespace App\Http\Controllers\supervisor_marketing;

use App\Events\PhcCreatedEvent;
use App\Http\Controllers\Controller;
use App\Models\DocumentPhc;
use App\Models\DocumentPreparation;
use App\Models\PHC;
use App\Models\PhcApproval;
use App\Models\Project;
use App\Models\User;
use App\Notifications\PhcAllApprovedNotification;
use App\Notifications\PhcValidationRequested;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class SupervisorPhcController extends Controller
{
    //
    public function create($project_id)
    {
        $project = Project::with('quotation', 'phc')->findOrFail($project_id);

        if ($project->phc) {
            return redirect()->route('phc.edit', $project->phc->id)
                ->with('info', 'PHC sudah dibuat, silakan edit.');
        }

        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', [
                'engineer',
                'engineer_supervisor',
                'project manager',
                'project controller',
                'engineering_admin',
                'supervisor marketing',
                'marketing_admin',
                'marketing_director',
                'marketing_estimator',
                'sales_supervisor'
            ]);
        })->with('role')->get();

        $phc = null;
        $documents = DocumentPhc::all(); // 🔹 Ambil semua dokumen

        return view('supervisor.phcs.form', compact('project', 'users', 'phc', 'documents'));
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
            'project_id' => 'required|exists:projects,pn_number',
            'handover_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'target_finish_date' => 'nullable|date',
            'client_pic_name' => 'required|string',
            'client_mobile' => 'nullable|string',
            'client_reps_office_address' => 'nullable|string',
            'client_site_representatives' => 'nullable|string',
            'client_site_address' => 'nullable|string',
            'site_phone_number' => 'nullable|string',
            'pic_marketing_id' => 'nullable|exists:users,id',
            'pic_engineering_id' => 'nullable|exists:users,id',
            'ho_marketings_id' => 'nullable|exists:users,id',
            'ho_engineering_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'retention' => 'nullable|string',
            'warranty' => 'nullable|string',
            'penalty' => 'nullable|string',

            // checklist dokumen per PHC
            'documents.*' => 'nullable|in:A,NA',
        ]);

        $validated['created_by'] = FacadesAuth::id();
        $validated['status'] = 'pending';

        // 🔹 Buat PHC dulu
        $phc = Phc::create($validated);

        // 🔹 Simpan Document Preparation
        if ($request->has('documents')) {
            foreach ($request->documents as $docId => $status) {
                DocumentPreparation::create([
                    'phc_id' => $phc->id,
                    'document_id' => $docId,
                    'is_applicable' => $status === 'A',
                    'date_prepared' => now(),
                ]);
            }
        }

        $allApproverIds = [];

        /**
         * 1. Approval HO Marketing & PIC Marketing
         */
        $marketingApprovers = array_filter([
            $request->ho_marketings_id,
            $request->pic_marketing_id
        ]);

        foreach ($marketingApprovers as $userId) {
            PhcApproval::create([
                'phc_id' => $phc->id,
                'user_id' => $userId,
                'status' => 'pending',
            ]);
            $allApproverIds[] = $userId;
        }

        /**
         * 2. HO Engineering Approval
         */
        if (!empty($request->ho_engineering_id)) {
            PhcApproval::create([
                'phc_id' => $phc->id,
                'user_id' => $request->ho_engineering_id,
                'status' => 'pending',
            ]);
            $allApproverIds[] = $request->ho_engineering_id;

            User::find($request->ho_engineering_id)?->notify(new PhcValidationRequested($phc));
        } else {
            $validatorUsers = User::whereHas('role', function ($q) {
                $q->whereIn('name', ['project manager', 'project controller', 'super_admin']);
            })->get();

            foreach ($validatorUsers as $user) {
                PhcApproval::create([
                    'phc_id' => $phc->id,
                    'user_id' => $user->id,
                    'status' => 'pending',
                ]);
                $user->notify(new PhcValidationRequested($phc));
                $allApproverIds[] = $user->id;
            }
        }

        // 🔹 Tidak ada approval untuk PIC Engineering

        // 🔹 Event approver
        $allApproverIds = array_unique($allApproverIds);
        event(new PhcCreatedEvent($phc, $allApproverIds));

        session()->flash('resetStep', true);

        return redirect()
            ->route('supervisor.project.show', $request->project_id)
            ->with('success', 'PHC created successfully and waiting for approval.');
    }

    public function edit(PHC $phc)
    {
        $project = $phc->project()->with('quotation')->first();
        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['supervisor marketing', 'administration marketing', 'engineer']);
        })->get();

        // ambil checklist document yang sudah disiapkan untuk PHC ini
        $documentPreparations = $phc->documentPreparations()
            ->with('documentPhc') // supaya tau nama master dokumennya
            ->get();

        return view('supervisor.phcs.form', compact('phc', 'project', 'users', 'documentPreparations'));
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
            'retention' => 'nullable|string',
            'warranty' => 'nullable|string',
            'penalty' => 'nullable|string',
        ]);

        // update data utama PHC
        $phc->update($validated);

        // update checklist dari master document_phc
        $documents = DocumentPhc::all();
        foreach ($documents as $document) {
            $value = $request->input("document_{$document->id}"); // misal name input="document_1"

            $phc->documentPreparations()->updateOrCreate(
                ['document_phc_id' => $document->id],
                ['value' => $value]
            );
        }

        session()->flash('resetStep', true);

        return redirect()->route('supervisor.project.show', $phc->project_id)
            ->with('success', 'PHC updated successfully.');
    }


    public function show(PHC $phc)
    {
        $phc->load('project.quotation');
        $project = $phc->project;
        return view('supervisor.phcs.show', compact('phc', 'project'));
    }
}
