<?php

namespace App\Http\Controllers\API\SUC;

use App\Http\Controllers\Controller;
use App\Models\PackingList;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackingListApiController extends Controller
{
    //

    public function index()
    {
        $lists = PackingList::with(['project', 'intPic', 'creator'])->get();
        return response()->json($lists);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pn_id' => 'nullable|exists:projects,pn_number',
            'destination' => 'required|string|max:255',
            'expedition_name' => 'nullable|string',
            'client_pic' => 'nullable|string',
            'pl_date' => 'nullable|date',
            'ship_date' => 'nullable|date',
            'pl_type' => 'required|in:internal,client,expedition',
            'int_pic' => 'nullable|exists:users,id',
            'receive_date' => 'nullable|date',
            'pl_return_date' => 'nullable|date',
            'remark' => 'nullable|string',
        ]);

        // ambil tahun sekarang
        $year = now()->format('Y');

        // cek nomor terakhir untuk tahun ini
        $last = PackingList::whereYear('created_at', $year)
            ->orderByDesc('created_at')
            ->first();

        $nextNumber = 1;

        if ($last) {
            // ambil nomor urut dari pl_number (contoh: PL/005/2025 → 005)
            $lastNumber = (int) substr($last->pl_number, 3, 3);
            $nextNumber = $lastNumber + 1;
        }

        // format nomor urut jadi 3 digit
        $numberFormatted = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // generate pl_number & pl_id
        $validated['pl_number'] = "PL/{$numberFormatted}/{$year}";
        $validated['pl_id'] = $numberFormatted . $year;
        $validated['created_by'] = auth()->id();

        $packingList = PackingList::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Packing List created successfully',
            'data' => $packingList,
        ]);
    }

    public function show($id)
    {
        $packingList = PackingList::with(['project', 'intPic', 'creator'])->findOrFail($id);
        return response()->json($packingList);
    }

    public function update(Request $request, $id)
    {
        $packingList = PackingList::findOrFail($id);

        $validated = $request->validate([
            'destination' => 'sometimes|string|max:255',
            'expedition_name' => 'nullable|string',
            'client_pic' => 'nullable|string',
            'pl_date' => 'nullable|date',
            'ship_date' => 'nullable|date',
            'pl_type' => 'sometimes|in:internal,client,expedition',
            'int_pic' => 'nullable|exists:users,id',
            'receive_date' => 'nullable|date',
            'pl_return_date' => 'nullable|date',
            'remark' => 'nullable|string',
        ]);

        $packingList->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Packing List updated successfully',
            'data' => $packingList,
        ]);
    }

    public function destroy($id)
    {
        $packingList = PackingList::findOrFail($id);
        $packingList->delete();

        return response()->json([
            'success' => true,
            'message' => 'Packing List deleted successfully',
        ]);
    }

    public function generateNumber()
    {
        $year = now()->format('Y');

        $last = PackingList::whereYear('created_at', $year)
            ->orderByDesc('created_at')
            ->first();

        $nextNumber = 1;

        if ($last) {
            $lastNumber = (int) substr($last->pl_number, 3, 3);
            $nextNumber = $lastNumber + 1;
        }

        $numberFormatted = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $pl_number = "PL/{$numberFormatted}/{$year}";
        $pl_id = $numberFormatted . $year;

        return response()->json([
            'pl_number' => $pl_number,
            'pl_id' => $pl_id
        ]);
    }

}
