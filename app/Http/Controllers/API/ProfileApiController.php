<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileApiController extends Controller
{
    //
    // Cek profil user saat ini
    public function profile()
    {
        $user = Auth::user()->load(['role', 'department']);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    // Update password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.'
        ]);
    }

    // Update PIN
    public function updatePin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6|confirmed',
        ]);

        $user = Auth::user();

        $user->update([
            'pin' => Hash::make($request->pin),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PIN berhasil diubah.'
        ]);
    }
}
