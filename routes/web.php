<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;     // PENTING: Untuk HTTPS di Vercel
use Illuminate\Support\Facades\Artisan; // PENTING: Untuk maintenance command
use App\Http\Controllers\ProfileController;

// --- IMPORT CONTROLLER AUTH ---
use App\Http\Controllers\Auth\OtpController;

// --- IMPORT CONTROLLER UMUM [BARU] ---
use App\Http\Controllers\SearchController; // <--- Tambahkan ini

// --- IMPORT CONTROLLER KASIR ---
use App\Http\Controllers\Kasir\DashboardController as KasirDashboard;
use App\Http\Controllers\Kasir\ItemController;
use App\Http\Controllers\Kasir\ProcurementController;

// --- IMPORT CONTROLLER DAPUR ---
use App\Http\Controllers\Dapur\KdsController;
use App\Http\Controllers\Dapur\InventoryController as DapurInventory;

// --- IMPORT CONTROLLER PEMBELI ---
use App\Http\Controllers\Pembeli\DashboardController as PembeliDashboard;
use App\Http\Controllers\Pembeli\OrderController;

// --- IMPORT CONTROLLER SUPPLIER ---
use App\Http\Controllers\Supplier\DashboardController as SupplierDashboard;
use App\Http\Controllers\Supplier\InventoryController as SupplierInventory;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- SETTING VERCEL: Paksa HTTPS ---
if (app()->environment('production')) {
    URL::forceScheme('https');
}

// 1. Route Utama
Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        return match($role) {
            'kasir'    => redirect()->route('kasir.dashboard'),
            'dapur'    => redirect()->route('dapur.dashboard'),
            'supplier' => redirect()->route('supplier.dashboard'),
            'pembeli'  => redirect()->route('pembeli.dashboard'),
            default    => redirect()->route('pembeli.dashboard'),
        };
    }
    return redirect()->route('login');
});

// Route OTP (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/verify-otp', [OtpController::class, 'create'])->name('otp.verify');
    Route::post('/verify-otp', [OtpController::class, 'store'])->name('otp.store');
    Route::post('/resend-otp', [OtpController::class, 'resendOtp'])->name('otp.resend');
});

// 2. Profil User & FITUR UMUM (Search)
Route::middleware('auth')->group(function () {
    // --- [BARU] ROUTE PENCARIAN ---
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Route Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// -----------------------------------------------------------------------------
// 3. ROUTE KASIR
// -----------------------------------------------------------------------------
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirDashboard::class, 'index'])->name('dashboard');
    Route::post('/order/{id}/selesai', [KasirDashboard::class, 'cetakStruk'])->name('order.selesai');
    Route::get('/items/print', [ItemController::class, 'printMenu'])->name('items.print');
    Route::resource('items', ItemController::class);
    Route::get('/procurement/print', [ProcurementController::class, 'print'])->name('procurement.print');
    Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement.index');
    Route::post('/procurement', [ProcurementController::class, 'store'])->name('procurement.store');
});

// -----------------------------------------------------------------------------
// 4. ROUTE DAPUR
// -----------------------------------------------------------------------------
Route::middleware(['auth', 'role:dapur'])->prefix('dapur')->name('dapur.')->group(function () {
    Route::get('/dashboard', [KdsController::class, 'index'])->name('dashboard');
    Route::patch('/order/{id}/update', [KdsController::class, 'updateStatus'])->name('order.update');
    Route::get('/inventory/print', [DapurInventory::class, 'print'])->name('inventory.print');
    Route::post('/inventory/{id}/kurangi', [DapurInventory::class, 'kurangiStok'])->name('inventory.kurangi');
    Route::resource('inventory', DapurInventory::class);
});

// -----------------------------------------------------------------------------
// 5. ROUTE PEMBELI
// -----------------------------------------------------------------------------
Route::middleware(['auth', 'role:pembeli'])->prefix('pembeli')->name('pembeli.')->group(function () {
    Route::get('/dashboard', [PembeliDashboard::class, 'index'])->name('dashboard');
    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
});

// -----------------------------------------------------------------------------
// 6. ROUTE SUPPLIER
// -----------------------------------------------------------------------------
Route::middleware(['auth', 'role:supplier'])->prefix('supplier')->name('supplier.')->group(function () {
    Route::get('/dashboard', [SupplierDashboard::class, 'index'])->name('dashboard');
    Route::get('/struk/{id}', [SupplierDashboard::class, 'cetakStruk'])->name('struk');
    Route::post('/confirm/{id}', [SupplierDashboard::class, 'confirmPayment'])->name('confirm');
    Route::post('/kirim/{id}', [SupplierDashboard::class, 'kirimBarang'])->name('kirim');
    Route::resource('products', SupplierInventory::class);
});

require __DIR__.'/auth.php';

// Route Debugging Cloudinary
Route::get('/fix-cloudinary', function () {
    try {
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $envUrl = env('CLOUDINARY_URL');
        $statusEnv = !empty($envUrl) ? '<span style="color:green; font-weight:bold;">✅ TERBACA</span>' : '<span style="color:red; font-weight:bold;">❌ TIDAK TERBACA</span>';
        return "Status Cloudinary: $statusEnv";
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});