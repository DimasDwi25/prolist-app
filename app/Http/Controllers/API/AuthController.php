<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials.'], 401);
        }

        $user = Auth::user();
        $role = $user->role->name ?? null;

        $roleRedirects = [
            'super_admin'          => '/admin',
            'marketing_director'   => '/marketing-director',
            'supervisor marketing' => '/marketing',
            'manager_marketing'    => '/marketing',
            'sales_supervisor'     => '/marketing',
            'marketing_admin'      => '/marketing',
            'estimator'            => '/estimator',
            'engineer'             => '/man-power',
            'project controller'   => '/engineer',
            'project manager'  => '/engineer',
            'engineering_director' => '/engineer',
            'marketing_estimator'  => '/marketing',
            'warehouse' => '/suc'
        ];

        return response()->json([
            'user'         => $user,
            'role'         => $role,
            'redirect_url' => $roleRedirects[$role] ?? '/unauthorized',
            'token'        => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60, // ✅ fix
        ]);
    }

    public function me()
    {
        return response()->json(Auth::user());
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out']);
    }

    public function refresh()
    {
        return response()->json([
            'token'      => auth()->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
