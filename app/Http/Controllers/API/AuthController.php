<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $user = $request->user();
        $role = $user->role->name;

        // Mapping role ke route
        $roleRedirects = [
            'super_admin'            => '/admin',
            'marketing_director'     => '/marketing-director',
            'supervisor marketing'   => '/marketing',
            'manager_marketing'      => '/marketing',
            'sales_supervisor'       => '/marketing',
            'marketing_admin'        => '/marketing',
            'estimator'              => '/estimator',
            'engineer'               => '/engineer',
            'project controller'     => '/engineer',
            'engineering_manager'    => '/engineer',
            'engineering_director'   => '/engineer',
            'marketing_estimator'    => '/marketing',
        ];

        // Default redirect kalau role tidak dikenali
        $redirectUrl = $roleRedirects[$role] ?? '/unauthorized';

        // Buat token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
            'role'         => $role,
            'redirect_url' => $redirectUrl,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}