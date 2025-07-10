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
            }

            if ($user->role->name === 'marketing') {
                return redirect()->intended('/marketing');
            }

            if ($user->role->name === 'engineer') {
                return redirect()->intended('/engineer');
            }

        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ]);
    }
}
