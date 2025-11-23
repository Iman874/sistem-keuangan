<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\PasswordVerification;

class PasswordVerificationController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function showVerifyCodeForm(Request $request)
    {
        $email = $request->email;
        return view('auth.verify-code', compact('email'));
    }

    public function verifyCode(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'code' => 'required|digits:6',
    ]);

    $user = User::where('email', $request->email)->first();
    if (!$user) {
        return back()->withErrors(['email' => 'Email tidak ditemukan']);
    }

    $verification = PasswordVerification::where('user_id', $user->id)
        ->where('code', $request->code)
        ->whereNull('used_at')
        ->where('expires_at', '>', now())
        ->latest()->first();

    if (!$verification) {
        return back()->withErrors(['code' => 'Kode verifikasi salah atau sudah kadaluarsa']);
    }

    // simpan kode ke session agar aman
    session(['verified_password_reset_user' => $user->id]);

    return redirect()->route('password.reset.form', ['email' => $user->email]);
}


    public function showResetForm(Request $request)
    {
        $email = $request->email;
        return view('auth.reset-password', compact('email'));
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan']);
        }

        // Generate kode 6 digit
        $code = random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        // Simpan ke database
        PasswordVerification::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => $expiresAt,
        ]);

        // Kirim email dengan template khusus kode verifikasi
        Mail::send('vendor.notifications.verify-code-email', [
            'code' => $code,
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Kode Verifikasi Reset Password');
        });

        // Redirect ke halaman input kode verifikasi
        return redirect()->route('password.verify.code.form', ['email' => $user->email])
            ->with('status', 'Kode verifikasi telah dikirim ke email Anda.');
    }

    public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
    ]);

    $user = User::where('email', $request->email)->first();
    if (!$user) {
        return back()->withErrors(['email' => 'Email tidak ditemukan']);
    }

    // cek apakah user memang lolos verifikasi kode
    if (session('verified_password_reset_user') !== $user->id) {
        return back()->withErrors(['email' => 'Kode verifikasi belum valid']);
    }

    // update password
    $user->password = bcrypt($request->password);
    $user->save();

    // tandai kode terakhir sebagai used
    PasswordVerification::where('user_id', $user->id)
        ->whereNull('used_at')
        ->latest()
        ->update(['used_at' => now()]);

    // hapus session
    session()->forget('verified_password_reset_user');

    return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login.');
}


}
