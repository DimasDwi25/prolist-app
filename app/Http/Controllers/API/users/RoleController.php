<?php

namespace App\Http\Controllers\API\users;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    //
    public function index()
    {
        return response()->json([
            'data' => Role::all(),
        ]);
    }

    public function roleOnlyType1()
    {
        $roles = Role::where('type_role', '1')->get();

        return response()->json([
            'success' => true,
            'data' => $roles,
        ]);
    }

   public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'type_role' => 'required|in:1,2',
        ]);

        $role = Role::create([
            'name'        => $request->name,
            'type_role'   => $request->type_role,
        ]);

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role,
        ], 201);
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);

        return response()->json(['data' => $role]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:100' . $role->id,
            'type_role'   => 'required|in:1,2',
        ]);

        $role->update([
            'name'        => $request->name,
            'type_role'   => $request->type_role,
        ]);

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => $role,
        ]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully',
        ]);
    }
}
