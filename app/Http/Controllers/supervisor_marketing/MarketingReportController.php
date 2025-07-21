<?php

namespace App\Http\Controllers\supervisor_marketing;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use Illuminate\Http\Request;

class MarketingReportController extends Controller
{
    //
    public function index()
    {
        $quotations = Quotation::with('client', 'user')->get();

        return view('supervisor.marketing-report.index', compact('quotations'));
    }
}
