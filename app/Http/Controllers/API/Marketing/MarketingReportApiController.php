<?php

namespace App\Http\Controllers\API\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use Illuminate\Http\Request;

class MarketingReportApiController extends Controller
{
    //
    public function index()
    {
        $quotations = Quotation::with('client', 'user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Sales report fetched successfully',
            'data'    => $quotations,
        ]);
    }
}
