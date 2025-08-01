<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse;

class LoginRedirectController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect dinamis berdasarkan role
            if ($user->role->name === 'super_admin') {
                return redirect()->intended('/admin');
            } elseif ($user->role->name === 'supervisor marketing') {
                return redirect()->intended('/supervisor-marketing');
            } elseif ($user->role->name === 'administration marketing') {
                return redirect()->intended('/administration-marketing');
            } elseif ($user->role->name === 'estimator') {
                return redirect()->intended('/estimator');
            } elseif ($user->role->name === 'engineer') {
                return redirect()->intended('/engineer');
            } elseif ($user->role->name === 'project controller') {
                return redirect()->intended('/project-controller');
            } elseif ($user->role->name === 'project manager') {
                return redirect()->intended('/project-manager');
            } 
            else {
                Auth::guard('web')->logout();
                return redirect()->route('login')->with('status', 'You are not authorized to access this page.');
            }

        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ]);
    }
}
