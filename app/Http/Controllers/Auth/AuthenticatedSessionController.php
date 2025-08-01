<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
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
        if (Auth::user() && Auth::user()->role->name == 'super_admin') {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user() && Auth::user()->role->name == 'supervisor marketing') {
            return redirect()->route('supervisor.dashboard');
        } elseif (Auth::user() && Auth::user()->role->name == 'administration marketing') {
            return redirect()->route('administration_marketing.dashboard');
        } elseif (Auth::user() && Auth::user()->role->name == 'estimator') {
            return redirect()->route('estimator.dashboard');
        } elseif (Auth::user() && Auth::user()->role->name == 'engineer') {
            return redirect()->route('engineer.dashboard');
        } elseif (Auth::user() && Auth::user()->role->name == 'project controller') {
            return redirect()->route('project_controller.dashboard');
        } elseif (Auth::user() && Auth::user()->role->name == 'project manager') {
            return redirect()->route('project_manager.dashboard');
        }
        else {
            Auth::guard('web')->logout();
            return redirect()->route('login')->with('status', 'You are not authorized to access this page.');
        }
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
