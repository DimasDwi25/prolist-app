<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\ScopeOfWorkProject;
use Illuminate\Http\Request;

class ScopeOfWorkProjectApiController extends Controller
{
    //
    /**
     * Menampilkan semua scope of work projects.
     */
    public function index()
    {
        $sow = ScopeOfWorkProject::with(['project', 'picUser'])->get();
        return response()->json($sow);
    }

    /**
     * Menyimpan scope of work project baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id'        => 'required|exists:projects,pn_number',
            'work_details'      => 'required|string',
            'pic'               => 'nullable|exists:users,id',
            'target_finish_date'=> 'nullable|date',
            'start_date'        => 'nullable|date',
            'finish_date'       => 'nullable|date',
        ]);

        $sow = ScopeOfWorkProject::create($request->all());

        return response()->json([
            'message' => 'Scope of Work berhasil ditambahkan',
            'data'    => $sow
        ], 201);
    }

    /**
     * Menampilkan detail scope of work project.
     */
    public function show($id)
    {
        $sow = ScopeOfWorkProject::with(['project', 'picUser'])->findOrFail($id);
        return response()->json($sow);
    }

    /**
     * Update scope of work project.
     */
    public function update(Request $request, $id)
    {
        $sow = ScopeOfWorkProject::findOrFail($id);

        $request->validate([
            'project_id'        => 'required|exists:projects,pn_number',
            'work_details'      => 'sometimes|string',
            'pic'               => 'sometimes|exists:users,id',
            'target_finish_date'=> 'sometimes|date',
            'start_date'        => 'sometimes|date',
            'finish_date'       => 'sometimes|date',
        ]);

        $sow->update($request->all());

        return response()->json([
            'message' => 'Scope of Work berhasil diupdate',
            'data'    => $sow
        ]);
    }

    /**
     * Hapus scope of work project.
     */
    public function destroy($id)
    {
        $sow = ScopeOfWorkProject::findOrFail($id);
        $sow->delete();

        return response()->json([
            'message' => 'Scope of Work berhasil dihapus'
        ]);
    }
}
