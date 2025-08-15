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
        $quotations = Quotation::with('client', 'user')->orderBy('quotations.created_at', 'desc')->get();
        return view('supervisor.quotation.index', compact('quotations'));
    }

    public function create()
    {
        $clients = Client::all();
        $nextNumber = Quotation::getNextQuotationNumber();
        $noQuotationNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $monthRoman = old('month_roman');
        $formattedQuotation = Quotation::formatFullQuotationNo($noQuotationNumber, $monthRoman);
        $quotation = new Quotation();

        return view('supervisor.quotation.form', compact(
            'quotation',
            'clients', 
            'noQuotationNumber', 
            'formattedQuotation', 
            'monthRoman'
        ));
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

        $number = str_pad($request->no_quotation, 3, '0', STR_PAD_LEFT);
        $quotationDate = \Carbon\Carbon::parse($request->quotation_date);
        $romanMonth = Quotation::convertMonthToRoman($quotationDate->format('m'));
        $yearShort = $quotationDate->format('y');

        $formattedNoQuotation = "Q-{$number}/{$romanMonth}/{$yearShort}";
        $quotationNumber = $quotationDate->format('Y') . $number;

        $quotation = new Quotation($request->except(['month_roman']));
        $quotation->status = $request->status ?? 'O';
        $quotation->quotation_date = $quotationDate;
        $quotation->no_quotation = $formattedNoQuotation;
        $quotation->quotation_number = $quotationNumber; // primary key

        $quotation->save();

        return redirect()->route('quotation.index')->with('success', 'Quotation created successfully!');
    }


    public function edit(Quotation $quotation)
    {
        $clients = Client::all();
        
        // Extract the Roman month from the existing quotation number
        $quotationParts = explode('/', $quotation->no_quotation);
        $romanMonth = $quotationParts[1] ?? null;
        
        $noQuotationNumber = substr($quotation->no_quotation, 2, 3); // Extract the numeric part (001, 002, etc.)
        $monthRoman = $romanMonth; // This is already in Roman format
        
        return view('supervisor.quotation.form', compact(
            'quotation',
            'clients', 
            'noQuotationNumber', 
            'monthRoman'
        ));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $this->validateRequest($request, $quotation->quotation_number);

        $number = str_pad($request->no_quotation, 3, '0', STR_PAD_LEFT);
        $quotationDate = \Carbon\Carbon::parse($request->quotation_date);
        $romanMonth = Quotation::convertMonthToRoman($quotationDate->format('m'));
        $yearShort = $quotationDate->format('y');

        $formattedNoQuotation = "Q-{$number}/{$romanMonth}/{$yearShort}";

        $quotation->update([
            ...$request->except(['no_quotation', 'month_roman']),
            'no_quotation' => $formattedNoQuotation,
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
        ])->validate();
    }

    public function show(Quotation $quotation)
    {
        // Eager load relationships to prevent N+1 queries
        $quotation->load([
            'client',
            'user',
        ]);

        // Prepare additional data if needed
        $statusOptions = [
            'A' => 'Completed',
            'D' => 'No PO Yet',
            'E' => 'Cancelled',
            'F' => 'Project Lost',
            'O' => 'On Going'
        ];

        // Get related quotations (example)
        $relatedQuotations = Quotation::where('client_id', $quotation->client_id)
                                    ->where('quotation_number', '!=', $quotation->quotation_number)
                                    ->latest()
                                    ->take(5)
                                    ->get();

        return view('supervisor.quotation.show', [
            'quotation' => $quotation,
            'statusOptions' => $statusOptions,
            'relatedQuotations' => $relatedQuotations,
            'pageTitle' => "Quotation: {$quotation->no_quotation}"
        ]);
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