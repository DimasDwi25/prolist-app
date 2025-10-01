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
            'project manager'      => '/engineer',
            'engineering_director' => '/engineer',
            'marketing_estimator'  => '/marketing',
            'warehouse'            => '/suc',
            'engineering_admin'    => '/engineer',
        ];

        // Buat refresh token
        $refreshToken = auth('api')->setTTL(config('jwt.refresh_ttl'))->attempt($credentials);

        return response()->json([
            'user'         => $user,
            'role'         => $role,
            'redirect_url' => $roleRedirects[$role] ?? '/unauthorized',
            'token'        => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
        ])->cookie(
            'refresh_token', // nama cookie
            $refreshToken,   // nilai refresh token
            config('jwt.refresh_ttl'), // waktu (menit)
            '/', null, true, true, false, 'Strict'
        );
    }

    public function refresh(Request $request)
    {
        try {
            $refreshToken = $request->cookie('refresh_token');
            if (!$refreshToken) {
                return response()->json(['error' => 'No refresh token'], 401);
            }

            $newToken = auth('api')->setToken($refreshToken)->refresh();

            return response()->json([
                'token'      => $newToken,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ])->cookie(
                'refresh_token',
                auth('api')->claims([])->setTTL(config('jwt.refresh_ttl'))->refresh(),
                config('jwt.refresh_ttl'),
                '/', null, true, true, false, 'Strict'
            );
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
