<?php

namespace App\Http\Controllers\API\users;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    //
    public function index()
    {
        $users = User::with('role')->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);

    }

    public function engineeringUsers()
    {
        $users = User::whereHas('role', function ($q) {
                $q->whereIn('name', [
                    'engineer',
                    'engineer_supervisor',
                    'project manager',
                    'project controller',
                    'engineering_admin',
                ]);
            })
            ->with('role')
            ->get();

        return response()->json([
            'success' => true,
            'category' => 'engineering',
            'data' => $users,
        ]);
    }

    public function marketingUsers()
    {
        $users = User::whereHas('role', function ($q) {
                $q->whereIn('name', [
                    'supervisor marketing',
                    'marketing_admin',
                    'marketing_director',
                    'marketing_estimator',
                    'sales_supervisor',
                ]);
            })
            ->with('role')
            ->get();

        return response()->json([
            'success' => true,
            'category' => 'marketing',
            'data' => $users,
        ]);
    }

    public function engineerOnly()
    {
        $users = User::whereHas('role', function ($q) {
                $q->whereIn('name', [
                    'engineer',
                    'electrician'
                ]);
            })
            ->with('role')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function roleTypeTwoOnly()
    {
        $roles = Role::where('type_role', 2)
            ->whereIn('name', ['engineer', 'electrician'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $roles,
        ]);
    }

    public function manPowerUsers()
    {
        $users = User::whereHas('role', function ($q) {
                $q->whereIn('name', [
                    'engineer',
                    'electrician',
                    'project manager',
                    'engineering_admin',
                ]);
            })
            ->with('role')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function manPowerRoles()
    {
        $roles = Role::where('type_role', 2)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $roles,
        ]);
    }


}
