<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

// --- TAMBAHAN IMPORT UNTUK OTP ---
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
// ---------------------------------

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan halaman pendaftaran.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Menangani request pendaftaran yang masuk.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input (Tetap pertahankan validasi Role Anda)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Validasi role sesuai kode asli Anda (termasuk supplier)
            'role' => ['required', 'in:kasir,pembeli,dapur,supplier'], 
        ]);

        // 2. Generate Kode OTP (6 Digit Angka)
        $otp = rand(100000, 999999);

        // 3. Buat User Baru di Database dengan Data OTP
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, // Simpan Role yang dipilih
            'otp_code' => $otp,       // Simpan Kode OTP
            'otp_expires_at' => Carbon::now()->addMinutes(5), // Berlaku 5 menit
        ]);

        // 4. Trigger Event Registered (Bawaan Laravel)
        event(new Registered($user));

        // 5. Kirim Email OTP
        // Gunakan try-catch agar error email tidak membatalkan pendaftaran user
        try {
            Mail::to($user->email)->send(new OtpMail($otp));
        } catch (\Exception $e) {
            // Log error jika diperlukan, tapi biarkan proses lanjut
        }

        // 6. Simpan Email di Session Sementara
        // Agar halaman verifikasi OTP tahu siapa yang sedang diproses
        session(['register_email' => $user->email]);

        // 7. MATIKAN Login Otomatis (PENTING UNTUK OTP)
        // Auth::login($user); <--- Jangan login dulu sebelum verifikasi!

        // 8. Redirect ke Halaman Input OTP
        // Logic redirect ke dashboard sesuai role dipindah nanti ke OtpController setelah sukses verifikasi
        return redirect()->route('otp.verify')->with('success', 'Registrasi berhasil! Kode OTP telah dikirim ke email Anda.');
    }
}