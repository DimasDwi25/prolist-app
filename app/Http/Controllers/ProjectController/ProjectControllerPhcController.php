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

        $documents = \App\Models\DocumentPhc::with(['preparations' => fn($q) => $q->where('phc_id', $phc->id)])->get();

        return view('project-controller.phcs.form', compact('phc', 'project', 'users', 'documents'));
    }


    public function update(Request $request, PHC $phc)
    {
        $validated = $request->validate([
            'pic_engineering_id' => 'nullable|exists:users,id',
            'documents' => 'array',
            'documents.*.status' => 'required|in:A,NA',
            'documents.*.date_prepared' => 'nullable|date',
        ]);

        if ($request->has('documents')) {
            foreach ($request->documents as $docId => $docData) {
                $status = $docData['status'] ?? 'NA';
                $datePrepared = $docData['date_prepared'] ?? null;

                \App\Models\DocumentPreparation::updateOrCreate(
                    ['phc_id' => $phc->id, 'document_id' => $docId],
                    [
                        'is_applicable' => $status === 'A',
                        // jika status A → ambil date dari input (boleh null)
                        // jika status NA → selalu null
                        'date_prepared' => $status === 'A' ? $datePrepared : null,
                    ]
                );
            }
        }

        $phc->update([
            'pic_engineering_id' => $validated['pic_engineering_id'] ?? null,
        ]);

        return redirect()->route('engineer.project.show', $phc->project_id)
            ->with('success', 'PHC updated successfully.');
    }


}
