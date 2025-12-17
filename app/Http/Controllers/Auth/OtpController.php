<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
// PENTING: Import Mail & Mailable agar fitur kirim email berfungsi
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    /**
     * 1. Menampilkan Halaman Input OTP
     */
    public function create()
    {
        // Cek apakah ada email di session (dari halaman register)
        // Jika tidak ada (user tembak url langsung), kembalikan ke login
        if (!session('register_email')) {
            return redirect()->route('login');
        }

        return view('auth.verify-otp');
    }

    /**
     * 2. Memproses Verifikasi Kode OTP
     */
    public function store(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $email = session('register_email');
        $user = User::where('email', $email)->first();

        // Jika user tidak ditemukan (Session expired atau hilang)
        if (!$user) {
            return redirect()->route('register')->withErrors(['email' => 'Sesi habis, silakan daftar ulang.']);
        }

        // Cek Kesesuaian Kode OTP
        if ($user->otp_code != $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah! Silakan cek email Anda.']);
        }

        // Cek Waktu Kadaluarsa
        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa. Silakan minta kirim ulang.']);
        }

        // --- SUKSES ---
        // 1. Bersihkan data OTP & Tandai Email Terverifikasi
        $user->forceFill([
            'otp_code' => null,
            'otp_expires_at' => null,
            'email_verified_at' => Carbon::now(),
        ])->save();

        // 2. Login User
        Auth::login($user);
        
        // 3. Hapus session email register
        $request->session()->forget('register_email');
        $request->session()->regenerate();

        // 4. Redirect ke Dashboard sesuai Role
        $url = match ($user->role) {
            'kasir'    => '/kasir/dashboard',
            'dapur'    => '/dapur/dashboard',
            'supplier' => '/supplier/dashboard',
            'pembeli'  => '/pembeli/dashboard',
            default    => '/pembeli/dashboard',
        };

        return redirect($url)->with('success', 'Selamat Datang! Akun berhasil diverifikasi.');
    }

    /**
     * 3. Fitur Kirim Ulang OTP (Resend)
     */
    public function resendOtp(Request $request)
    {
        $email = session('register_email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('login');
        }

        // Generate OTP Baru
        $otp = rand(100000, 999999);
        
        // Update Database dengan OTP baru & perpanjang waktu 5 menit
        $user->forceFill([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ])->save();

        // Kirim Email
        try {
            Mail::to($user->email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            // Tampilkan pesan error asli agar kita tahu kenapa gagal (SMTP/Password/Port)
            return back()->withErrors(['otp' => 'Gagal kirim email: ' . $e->getMessage()]);
        }

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda!');
    }
}