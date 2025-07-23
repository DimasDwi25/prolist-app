<?php

namespace App\Http\Controllers\supervisor_marketing;

use App\Http\Controllers\Controller;
use App\Imports\QuotationsImport;
use App\Models\Client;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SupervisorQuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::with('client', 'user')->get();
        return view('supervisor.quotation.index', compact('quotations'));
    }

    public function create()
    {
        $clients = Client::all();
        $nextNumber = Quotation::getNextQuotationNumber();
        $noQuotationNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $formattedQuotation = Quotation::formatFullQuotationNo($noQuotationNumber);

        return view('supervisor.quotation.form', compact('clients', 'noQuotationNumber', 'formattedQuotation'));
    }

    public function ajaxClients(Request $request)
    {
        $search = $request->q;

        $clients = Client::query()
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name']);

        return response()->json(
            $clients->map(fn($client) => [
                'id' => $client->id,
                'text' => $client->name,
            ])
        );
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);

        Quotation::create([
            ...$request->except(['no_quotation']),
            'no_quotation' => $request->no_quotation,
            'quotation_number' => $request->no_quotation,
        ]);

        return redirect()->route('quotation.index')->with('success', 'Quotation created successfully!');
    }

    public function edit(Quotation $quotation)
    {
        $clients = Client::all();
        $noQuotationNumber = str_pad($quotation->quotation_number ?? 1, 3, '0', STR_PAD_LEFT);

        return view('supervisor.quotation.form', compact('quotation', 'clients', 'noQuotationNumber'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $this->validateRequest($request, $quotation->id);

        $quotation->update([
            ...$request->except(['no_quotation']),
            'no_quotation' => $request->no_quotation,
        ]);

        return redirect()->route('quotation.index')->with('success', 'Quotation updated successfully!');
    }

    public function destroy(Quotation $quotation)
    {
        if (Auth::user()->role->name !== 'super_admin') {
            abort(403, 'Unauthorized');
        }

        $quotation->delete();

        return redirect()->route('quotation.index')->with('success', 'Quotation deleted successfully!');
    }

    private function validateRequest(Request $request, $id = null)
    {
        Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'client_pic' => 'required|string|max:255',
            'inquiry_date' => 'required|date',
            'title_quotation' => 'required|string|max:255',
            'quotation_date' => 'required|date',
            'no_quotation' => 'required|numeric|min:1',
            'quotation_weeks' => 'nullable|string',
            'quotation_value' => 'required|numeric|min:0',
            'revision_quotation_date' => 'nullable|date',
            'revisi' => 'nullable|string|max:255',
            'status' => 'nullable|in:A,D,E,F,O',
            'po_date' => 'nullable|date',
            'po_number' => 'nullable|string|max:255',
            'po_value' => 'nullable|numeric|min:0',
            'sales_weeks' => 'nullable|string',
        ])->validate();
    }

    public function show(Quotation $quotation)
    {
        return view('supervisor.quotation.show', compact('quotation'));
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        Validator::make($request->all(), [
            'status' => 'required|in:A,D,E,F,O',
        ])->validate();

        $quotation->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Quotation status updated successfully.');
    }

    
}