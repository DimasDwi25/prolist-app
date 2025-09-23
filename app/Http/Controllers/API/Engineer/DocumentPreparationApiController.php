<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\DocumentPreparation;
use Illuminate\Http\Request;

class DocumentPreparationApiController extends Controller
{
    //
    public function index($phcId)
    {
        $preparations = DocumentPreparation::with('document')
            ->where('phc_id', $phcId)
            ->get()
            ->groupBy('document_id')
            ->map(function ($items) {
                $doc = $items->first()->document;
                return [
                    'id' => $doc->id,
                    'name' => $doc->name,
                    'preparations' => $items->map(function ($prep) {
                        return [
                            'is_applicable' => $prep->is_applicable,
                            'date_prepared' => optional($prep->date_prepared)->toDateString(),
                        ];
                    })->values(),
                ];
            })
            ->values();

        return response()->json([
            'documents' => $preparations
        ]);
    }

}
