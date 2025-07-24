<?php

namespace App\Http\Controllers\supervisor_marketing;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Quotation;
use Illuminate\Http\Request;

class SupervisorDashboardController extends Controller
{
    //
    public function index()
    {
        return view('supervisor.dashboard', [
            'totalQuotation' => Quotation::count(),
            'totalQuotationValue' => Quotation::sum('quotation_value'),
            'totalSalesValue' => Quotation::sum('po_value'),
            'totalProject' => Project::count(),
            'outstandingQuotation' => Quotation::where('status', 'O')->count(),
            // 'outstandingProject' => Project::where('status_project', 'open')->count(),
        ]);
    }
}
