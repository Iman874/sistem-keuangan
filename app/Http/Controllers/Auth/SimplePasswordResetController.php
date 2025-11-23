<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class SimplePasswordResetController extends Controller
{
    /**
     * Tampilkan form untuk memasukkan email
     */
    public function showEmailForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses email dan cek keberadaannya
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'kasir')
            ->first();

        if (!$user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Email tidak ditemukan atau bukan milik kasir.']);
        }

        // Simpan ID user di session untuk digunakan di halaman reset password
        session(['reset_user_id' => $user->id]);

        // Redirect ke halaman reset password
        return redirect()->route('password.reset.simple');
    }

    /**
     * Tampilkan form untuk reset password
     */
    public function showResetForm()
    {
        // Cek apakah ada user_id di session
        if (!session('reset_user_id')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Silakan masukkan email Anda terlebih dahulu.']);
        }

        return view('auth.reset-password-simple');
    }

    /**
     * Proses reset password
     */
    public function resetPassword(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Cek apakah ada user_id di session
        if (!session('reset_user_id')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi reset password telah berakhir. Silakan coba lagi.']);
        }

        // Ambil user berdasarkan ID
        $user = User::where('id', session('reset_user_id'))
            ->where('role', 'kasir')
            ->first();

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'User tidak ditemukan. Silakan coba lagi.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus session
        session()->forget('reset_user_id');

        // Redirect ke login dengan pesan sukses
        return redirect()->route('login')
            ->with('status', 'Password berhasil diubah. Silakan login dengan password baru Anda.');
    }
}
