<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    //
    public function index()
    {
        return view('account.index');
    }

    // Halaman form ubah password
    public function editPassword()
    {
        return view('account.password');
    }

    // Proses ubah password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (! Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('account.index')->with('success', 'Password berhasil diubah.');
    }

    // Halaman form ubah PIN
    public function editPin()
    {
        return view('account.pin');
    }

    // Proses ubah PIN
    public function updatePin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6|confirmed', // PIN 6 digit
        ]);

        Auth::user()->update([
            'pin' => Hash::make($request->pin),
        ]);

        return redirect()->route('account.index')->with('success', 'PIN berhasil diubah.');
    }

}
