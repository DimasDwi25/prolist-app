<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Mapping role ke nama route dashboard
        $roleRedirects = [
            'super_admin'             => 'admin.dashboard',
            'marketing_director'      => 'marketing_director.dashboard',
            'supervisor marketing'    => 'marketing.dashboard',
            'manager_marketing'       => 'marketing.dashboard',
            'sales_supervisor'        => 'marketing.dashboard',
            'marketing_admin'=> 'marketing.dashboard',
            'estimator'               => 'estimator.dashboard',
            'engineer'                => 'engineer.dashboard',
            'project controller'      => 'engineer.dashboard',
            'engineering_manager'         => 'engineer.dashboard',
        ];

        // Redirect sesuai role
        if ($user && array_key_exists($user->role->name, $roleRedirects)) {
            return redirect()->route($roleRedirects[$user->role->name]);
        }

        // Kalau role tidak dikenali
        Auth::guard('web')->logout();
        return redirect()->route('login')->with('status', 'You are not authorized to access this page.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
