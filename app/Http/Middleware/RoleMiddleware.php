<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  Role yang diizinkan (misal: 'kasir', 'dapur', 'pembeli')
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect('login');
        }

        // 2. Cek apakah role user sesuai dengan parameter yang diminta route
        // Contoh: Route::middleware(['role:kasir']) -> $role adalah 'kasir'
        if (Auth::user()->role !== $role) {
            // Jika tidak sesuai, tampilkan halaman Error 403 (Forbidden)
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        // 3. Jika lolos pengecekan, lanjutkan request
        return $next($request);
    }
}