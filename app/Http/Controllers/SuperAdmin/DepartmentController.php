<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::all();
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.departments.create')->with('success', 'Department Created.');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Department::create($request->only('name'));
        return redirect()->route('admin.department')->with('success', 'Department created.');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $department->update($request->only('name'));
        return redirect()->route('admin.department')->with('success', 'Department updated.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('admin.department')->with('success', 'Department deleted.');
    }
}
