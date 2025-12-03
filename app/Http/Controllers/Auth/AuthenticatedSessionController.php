<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Menangani request login yang masuk.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Proses autentikasi (Cek email & password)
        $request->authenticate();

        // 2. Regenerate session ID
        $request->session()->regenerate();

        // 3. Ambil role user yang sedang login
        $role = $request->user()->role;

        // 4. Tentukan URL tujuan berdasarkan role (UPDATED: Ada Supplier)
        $url = match ($role) {
            'kasir'    => '/kasir/dashboard',
            'dapur'    => '/dapur/dashboard',
            'supplier' => '/supplier/dashboard', // TAMBAHAN PENTING
            'pembeli'  => '/pembeli/dashboard',
            default    => '/pembeli/dashboard',
        };

        // 5. Redirect user ke URL yang sudah ditentukan
        return redirect()->intended($url);
    }

    /**
     * Menangani proses logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}