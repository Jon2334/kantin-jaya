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
        // 1. Validasi Input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // PENTING: Tambahkan 'supplier' di sini agar validasi lolos
            'role' => ['required', 'in:kasir,pembeli,dapur,supplier'], 
        ]);

        // 2. Buat User Baru di Database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, // Simpan Role yang dipilih
        ]);

        // 3. Trigger Event Registered (Bawaan Laravel)
        event(new Registered($user));

        // 4. Login Otomatis setelah daftar
        Auth::login($user);

        // 5. Redirect Sesuai Role
        // Mengarahkan user ke dashboard masing-masing setelah register sukses
        $url = match ($user->role) {
            'kasir'    => '/kasir/dashboard',
            'dapur'    => '/dapur/dashboard',
            'supplier' => '/supplier/dashboard', // Redirect khusus Supplier
            'pembeli'  => '/pembeli/dashboard',
            default    => '/pembeli/dashboard',
        };

        return redirect($url);
    }
}