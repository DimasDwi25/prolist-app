<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\DocumentPhc;
use Illuminate\Http\Request;

class DocumentApiController extends Controller
{
    //
    // GET /api/document-phc
    public function index()
    {
        return response()->json(DocumentPhc::with('preparations')->get());
    }
    
    // GET /api/document-phc/{id}
    public function show($id)
    {
        $document = DocumentPhc::with('preparations')->find($id);

        if (!$document) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($document);
    }

    // POST /api/document-phc
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $document = DocumentPhc::create($request->only('name'));

        return response()->json($document, 201);
    }

    // PUT /api/document-phc/{id}
    public function update(Request $request, $id)
    {
        $document = DocumentPhc::find($id);

        if (!$document) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $document->update($request->only('name'));

        return response()->json($document);
    }

    // DELETE /api/document-phc/{id}
    public function destroy($id)
    {
        $doc = DocumentPhc::findOrFail($id);

        // hapus anak
        $doc->preparations()->delete();

        // hapus parent
        $doc->delete();


        return response()->json(['message' => 'Deleted successfully']);
    }
}
