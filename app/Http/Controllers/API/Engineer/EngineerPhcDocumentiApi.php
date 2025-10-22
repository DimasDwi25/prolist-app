<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\PHC;
use App\Models\User;
use App\Models\DocumentPreparation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EngineerPhcDocumentiApi extends Controller
{
    //
    public function show(PHC $phc)
    {
        $phc->load([
            'hoMarketing',
            'hoEngineering',
            'picEngineering',
            'picMarketing',
            'createdBy',
        ]);

        $project = $phc->project()->with('quotation')->first();

        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', [
                'supervisor marketing',
                'engineer',
                'project manager',
                'super_admin'
            ]);
        })->get();

        $documents = \App\Models\DocumentPhc::with([
            'preparations' => fn($q) => $q->where('phc_id', $phc->id)
        ])->get();

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
            'documents.*.document_id' => 'required|integer', // Add validation for document_id
            'documents.*.status' => 'required|in:A,NA',
            'documents.*.date_prepared' => 'nullable|date',
        ]);

        if ($request->has('documents')) {
            foreach ($request->documents as $docData) {
                // Cast to integer to ensure proper type
                $docId = (int) $docData['document_id'];
                
                $status = $docData['status'] ?? 'NA';
                $datePrepared = $docData['date_prepared'] ?? null;

                DocumentPreparation::updateOrCreate(
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

    /**
     * Upload document file for Document Preparation
     */
    public function uploadDocument(Request $request, $documentPreparationId)
    {
        $request->validate([
            'document' => 'required|file|max:10240', // 10MB max
        ]);

        $documentPreparation = DocumentPreparation::findOrFail($documentPreparationId);

        if ($request->hasFile('document')) {
            // Delete old file if exists
            if ($documentPreparation->attachment_path && Storage::exists($documentPreparation->attachment_path)) {
                Storage::delete($documentPreparation->attachment_path);
            }

            $path = $request->file('document')->store('document_preparations', 'public');

            try {
                $documentPreparation->attachment_path = $path;
                $documentPreparation->save();

                return response()->json([
                    'message' => 'Document uploaded successfully',
                    'data' => $documentPreparation
                ]);
            } catch (\Exception $e) {
                // Delete uploaded file if save fails
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
                return response()->json(['message' => 'Failed to save document: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    /**
     * View/Download attachment file for Document Preparation
     */
    public function viewAttachment($documentPreparationId)
    {
        $documentPreparation = DocumentPreparation::findOrFail($documentPreparationId);

        if (
            !$documentPreparation->attachment_path ||
            !Storage::disk('public')->exists($documentPreparation->attachment_path)
        ) {
            return response()->json(['message' => 'Attachment not found'], 404);
        }

        $filePath = storage_path('app/public/' . $documentPreparation->attachment_path);

        $mimeType = mime_content_type($filePath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($documentPreparation->attachment_path) . '"'
        ]);

    }
}
