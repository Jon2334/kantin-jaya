<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Mail\OtpMail; // Jangan lupa import ini
use Illuminate\Support\Facades\Mail; // Jangan lupa import ini

class OtpController extends Controller
{
    // 1. Tampilkan Halaman Input OTP
    public function create()
    {
        // Cek apakah ada email di session. Jika tidak, tendang ke login.
        if (!session('register_email')) {
            return redirect()->route('login');
        }
        return view('auth.verify-otp');
    }

    // 2. Proses Verifikasi OTP
    public function store(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $email = session('register_email');
        $user = User::where('email', $email)->first();

        // Jika user tidak ditemukan (session expired)
        if (!$user) {
            return redirect()->route('register')->withErrors(['email' => 'Sesi habis, silakan daftar ulang.']);
        }

        // Cek Kesesuaian Kode
        if ($user->otp_code != $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah!']);
        }

        // Cek Kadaluarsa
        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa. Silakan minta kirim ulang.']);
        }

        // --- SUKSES ---
        // Bersihkan data OTP dan tandai email verified
        $user->forceFill([
            'otp_code' => null,
            'otp_expires_at' => null,
            'email_verified_at' => Carbon::now(),
        ])->save();

        // Login User
        Auth::login($user);
        
        // Hapus session email
        $request->session()->forget('register_email');
        $request->session()->regenerate();

        // Redirect sesuai Role
        $url = match ($user->role) {
            'kasir'    => '/kasir/dashboard',
            'dapur'    => '/dapur/dashboard',
            'supplier' => '/supplier/dashboard',
            default    => '/pembeli/dashboard',
        };

        return redirect($url);
    }

    // 3. LOGIKA KIRIM ULANG OTP (BARU)
    public function resendOtp(Request $request)
    {
        $email = session('register_email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('login');
        }

        // Generate OTP Baru
        $otp = rand(100000, 999999);
        
        // Update Database
        $user->forceFill([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ])->save();

        // Kirim Email Lagi
        try {
            Mail::to($user->email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            return back()->withErrors(['otp' => 'Gagal mengirim ulang email. Cek koneksi internet.']);
        }

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda!');
    }
}