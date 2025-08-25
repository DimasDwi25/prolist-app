<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginRedirectController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
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
                'engineering_manager'        => '/engineer',
            ];

            // Cari redirect path sesuai role
            if (array_key_exists($role, $roleRedirects)) {
                return redirect()->intended($roleRedirects[$role]);
            }

            // Jika role tidak dikenali
            Auth::guard('web')->logout();
            return redirect()->route('login')->with('status', 'You are not authorized to access this page.');
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ]);
    }
}
