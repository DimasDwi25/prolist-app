<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\PHC;
use App\Models\User;
use Illuminate\Http\Request;

class EngineerPhcDocumentiApi extends Controller
{
    //
     public function show(PHC $phc)
    {
        $project = $phc->project()->with('quotation')->first();

        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['supervisor marketing', 'engineer', 'project manager', 'super_admin']);
        })->get();

        $documents = \App\Models\DocumentPhc::with(['preparations' => fn($q) => $q->where('phc_id', $phc->id)])->get();

        return response()->json([
            'phc' => $phc,
            'project' => $project,
            'users' => $users,
            'documents' => $documents,
        ]);
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
                        'date_prepared' => $status === 'A' ? $datePrepared : null,
                    ]
                );
            }
        }

        $phc->update([
            'pic_engineering_id' => $validated['pic_engineering_id'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PHC updated successfully',
            'phc' => $phc->fresh(),
        ]);
    }
}
