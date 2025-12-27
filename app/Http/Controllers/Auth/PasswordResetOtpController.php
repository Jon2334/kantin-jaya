<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail; // Kita pakai mail yang sama
use Carbon\Carbon;

class PasswordResetOtpController extends Controller
{
    // --- LANGKAH 1: MINTA EMAIL ---
    public function showEmailForm()
    {
        return view('auth.forgot-password-otp');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        // Generate OTP
        $otp = rand(100000, 999999);
        
        // Simpan OTP ke user (Reuse kolom yang sudah ada)
        $user->forceFill([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ])->save();

        // Kirim Email
        try {
            Mail::to($user->email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Gagal kirim email: ' . $e->getMessage()]);
        }

        // Simpan email di session untuk langkah selanjutnya
        session(['reset_email' => $user->email]);

        return redirect()->route('password.otp.verify');
    }

    // --- LANGKAH 2: VERIFIKASI OTP ---
    public function showOtpForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request.otp');
        }
        return view('auth.reset-otp-verify');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric|digits:6']);

        $email = session('reset_email');
        $user = User::where('email', $email)->first();

        if (!$user || $user->otp_code != $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'OTP Kadaluarsa.']);
        }

        // Tandai bahwa OTP sudah benar, boleh lanjut ke ganti password
        session(['otp_verified_for_reset' => true]);

        return redirect()->route('password.reset.form');
    }

    // --- LANGKAH 3: GANTI PASSWORD ---
    public function showResetForm()
    {
        // Cegah akses tembak langsung tanpa verifikasi OTP
        if (!session('reset_email') || !session('otp_verified_for_reset')) {
            return redirect()->route('password.request.otp');
        }
        return view('auth.reset-password-form');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:8',
        ]);

        $email = session('reset_email');
        $user = User::where('email', $email)->first();

        // Update Password
        $user->forceFill([
            'password' => Hash::make($request->password),
            'otp_code' => null, // Hapus OTP bekas
            'otp_expires_at' => null,
        ])->save();

        // Bersihkan session
        $request->session()->forget(['reset_email', 'otp_verified_for_reset']);

        return redirect()->route('login')->with('success', 'Password berhasil diubah! Silakan login.');
    }
}   