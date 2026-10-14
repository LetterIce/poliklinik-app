<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil pengguna dan form ganti password
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'dokter') {
            $user->load('poli');
        }

        return view('profile.index', compact('user'));
    }

    /**
     * Update password pengguna yang sedang login
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();

        // Update password yang di-hash
        $user->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Password akun Anda berhasil diubah!');
    }
}
