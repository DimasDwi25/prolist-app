<?php

namespace App\Http\Controllers\API\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MarketingQuotationController extends Controller
{
    //
    // Semua route akan pakai middleware jwt
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // List semua quotation
    public function index()
    {
        $quotations = Quotation::with('client', 'user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($quotations);
    }

    // Show detail quotation
    public function show(Quotation $quotation)
    {
        $quotation->load(['client', 'user']);

        return response()->json($quotation);
    }

    // Store quotation baru
    public function store(Request $request)
    {
        $this->validateRequest($request);

        $number = str_pad($request->no_quotation, 3, '0', STR_PAD_LEFT);
        $quotationDate = \Carbon\Carbon::parse($request->quotation_date);
        $formattedNoQuotation = Quotation::formatFullQuotationNo($number, $quotationDate);
        $quotationNumber = $quotationDate->format('Y') . $number;

        $quotation = new Quotation($request->except(['month_roman']));
        $quotation->status = $request->status ?? 'O';
        $quotation->quotation_date = $quotationDate;
        $quotation->no_quotation = $formattedNoQuotation;
        $quotation->quotation_number = $quotationNumber;
        $quotation->user_id = Auth::id();

        $quotation->save();

        return response()->json([
            'message' => 'Quotation created successfully',
            'quotation' => $quotation
        ], 201);
    }

    // Update quotation
    public function update(Request $request, Quotation $quotation)
    {
        $this->validateRequest($request, $quotation->quotation_number);

        // Update kecuali no_quotation & month_roman
        $quotation->update($request->except(['no_quotation', 'month_roman']));

        // Return lengkap dengan relasi supaya frontend tidak kosong
        return response()->json([
            'message' => 'Quotation updated successfully',
            'quotation' => $quotation->load('client')
        ]);
    }



    // Delete quotation
    public function destroy(Quotation $quotation)
    {
        if (Auth::user()->role->name !== 'super_admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $quotation->delete();

        return response()->json(['message' => 'Quotation deleted successfully']);
    }

    // Ajax search clients
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

    // Validasi request
    private function validateRequest(Request $request, $id = null)
    {
        Validator::make($request->all(), [
            'client_id' => 'sometimes|required|exists:clients,id',
            'client_pic' => 'sometimes|required|string|max:255',
            'inquiry_date' => 'sometimes|required|date',
            'title_quotation' => 'sometimes|required|string|max:255',
            'quotation_date' => 'sometimes|required|date',
            'no_quotation' => 'sometimes|required|string|min:1',
            'quotation_weeks' => 'sometimes|nullable|string',
            'quotation_value' => 'sometimes|required|numeric|min:0',
            'revision_quotation_date' => 'nullable|date',
            'revisi' => 'nullable|string|max:255',
            'status' => 'nullable|in:A,D,E,F,O',
        ])->validate();
    }

    public function nextNumber()
    {
        $year = now()->year;
        $next = Quotation::getNextQuotationNumberForYear($year);

        return response()->json([
            'next_number' => str_pad($next, 3, '0', STR_PAD_LEFT),
            'year' => $year
        ]);
    }
}
