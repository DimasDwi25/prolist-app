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

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = auth('api')->user();
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
            'project manager'      => '/engineer',
            'engineering_director' => '/engineer',
            'marketing_estimator'  => '/marketing',
            'warehouse'            => '/suc',
            'engineering_admin'    => '/engineer',
        ];

        return response()->json([
            'user'        => $user,
            'role'        => $role,
            'redirect_url'=> $roleRedirects[$role] ?? '/unauthorized',
            'token'       => $token,
            'token_type'  => 'bearer',
            'expires_in'  => auth('api')->factory()->getTTL() * 60,
        ]);
    }


    public function refresh()
    {
        try {
            return response()->json([
                'token' => auth('api')->refresh(),
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Refresh failed'], 401);
        }
    }




    public function me()
    {
        return response()->json(Auth::user());
    }

    public function logout()
    {
        try {
            auth('api')->logout();
        } catch (\Exception $e) {
            // Token might not be provided or invalid, but still return success
        }
        return response()->json(['message' => 'Logged out']);
    }

}
