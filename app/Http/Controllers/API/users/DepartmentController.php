<?php

namespace App\Http\Controllers\API\users;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    //
    public function index()
    {
        return response()->json([
            'data' => Department::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $department = Department::create($request->only('name'));

        return response()->json([
            'message' => 'Department created successfully',
            'data' => $department,
        ], 201);
    }

    public function show($id)
    {
        $department = Department::findOrFail($id);

        return response()->json(['data' => $department]);
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100' . $department->id,
        ]);

        $department->update($request->only('name'));

        return response()->json([
            'message' => 'Department updated successfully',
            'data' => $department,
        ]);
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return response()->json([
            'message' => 'Department deleted successfully',
        ]);
    }
}
