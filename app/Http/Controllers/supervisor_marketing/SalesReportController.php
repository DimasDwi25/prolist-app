<?php

namespace App\Http\Controllers\supervisor_marketing;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    //
    public function index()
    {
        $quotations = Quotation::with('client', 'user')->get();

        return view('supervisor.sales-report.index', compact('quotations'));
    }
}
