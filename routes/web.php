<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;     // PENTING: Untuk HTTPS di Vercel
use Illuminate\Support\Facades\Artisan; // PENTING: Untuk maintenance command
use App\Http\Controllers\ProfileController;

// --- IMPORT CONTROLLER AUTH (Termasuk OTP) ---
use App\Http\Controllers\Auth\OtpController; // [BARU] Tambahkan ini

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

// --- SETTING VERCEL: Paksa HTTPS (Wajib untuk Cloudinary & Keamanan) ---
if (app()->environment('production')) {
    URL::forceScheme('https');
}

// 1. Route Utama (Redirect Otomatis berdasarkan Role)
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

// -----------------------------------------------------------------------------
// [BARU] ROUTE OTP (VERIFIKASI EMAIL)
// Diletakkan di luar middleware 'auth' karena user belum login saat input OTP
// -----------------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/verify-otp', [OtpController::class, 'create'])->name('otp.verify');
    Route::post('/verify-otp', [OtpController::class, 'store'])->name('otp.store');
    Route::post('/resend-otp', [OtpController::class, 'resendOtp'])->name('otp.resend');
});

// 2. Profil User
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// -----------------------------------------------------------------------------
// 3. ROUTE KASIR (Group name: 'kasir.')
// -----------------------------------------------------------------------------
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [KasirDashboard::class, 'index'])->name('dashboard');
    
    // Transaksi
    Route::post('/order/{id}/selesai', [KasirDashboard::class, 'cetakStruk'])->name('order.selesai');
    
    // Cetak Menu
    Route::get('/items/print', [ItemController::class, 'printMenu'])->name('items.print');
    
    // Kelola Stok Menu (CRUD + Upload Cloudinary ada di sini)
    Route::resource('items', ItemController::class);
    
    // Belanja Stok ke Supplier
    Route::get('/procurement/print', [ProcurementController::class, 'print'])->name('procurement.print');
    Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement.index');
    Route::post('/procurement', [ProcurementController::class, 'store'])->name('procurement.store');
});

// -----------------------------------------------------------------------------
// 4. ROUTE DAPUR (Group name: 'dapur.')
// -----------------------------------------------------------------------------
Route::middleware(['auth', 'role:dapur'])->prefix('dapur')->name('dapur.')->group(function () {
    // KDS (Kitchen Display System)
    Route::get('/dashboard', [KdsController::class, 'index'])->name('dashboard');
    Route::patch('/order/{id}/update', [KdsController::class, 'updateStatus'])->name('order.update');
    
    // Inventory Bahan Baku
    Route::get('/inventory/print', [DapurInventory::class, 'print'])->name('inventory.print');
    Route::post('/inventory/{id}/kurangi', [DapurInventory::class, 'kurangiStok'])->name('inventory.kurangi');
    Route::resource('inventory', DapurInventory::class);
});

// -----------------------------------------------------------------------------
// 5. ROUTE PEMBELI (Group name: 'pembeli.')
// -----------------------------------------------------------------------------
Route::middleware(['auth', 'role:pembeli'])->prefix('pembeli')->name('pembeli.')->group(function () {
    Route::get('/dashboard', [PembeliDashboard::class, 'index'])->name('dashboard');
    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
});

// -----------------------------------------------------------------------------
// 6. ROUTE SUPPLIER (Group name: 'supplier.')
// -----------------------------------------------------------------------------
Route::middleware(['auth', 'role:supplier'])->prefix('supplier')->name('supplier.')->group(function () {
    Route::get('/dashboard', [SupplierDashboard::class, 'index'])->name('dashboard');
    Route::get('/struk/{id}', [SupplierDashboard::class, 'cetakStruk'])->name('struk');
    Route::post('/confirm/{id}', [SupplierDashboard::class, 'confirmPayment'])->name('confirm');
    Route::post('/kirim/{id}', [SupplierDashboard::class, 'kirimBarang'])->name('kirim');
    
    // Kelola Katalog Barang
    Route::resource('products', SupplierInventory::class);
});

require __DIR__.'/auth.php';

// =========================================================================
// 7. ROUTE PENYELAMAT (DEBUGGING VERCEL)
// Akses route ini di: https://nama-app-anda.vercel.app/fix-cloudinary
// =========================================================================
Route::get('/fix-cloudinary', function () {
    try {
        // 1. Bersihkan Cache (Penting setelah update env di Vercel)
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        // 2. Cek Koneksi ENV
        $envUrl = env('CLOUDINARY_URL');
        
        // 3. Output Status
        $statusEnv = !empty($envUrl) 
            ? '<span style="color:green; font-weight:bold;">✅ TERBACA</span>' 
            : '<span style="color:red; font-weight:bold;">❌ TIDAK TERBACA / NULL</span>';

        return "
            <div style='font-family: sans-serif; padding: 40px; max-width: 600px; margin: 0 auto; background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px;'>
                <h1 style='color: #333;'>🛠️ Status Konfigurasi Cloudinary</h1>
                <p>Route ini berfungsi untuk membersihkan cache dan mengecek apakah Environment Variables Vercel sudah masuk ke Laravel.</p>
                
                <hr style='margin: 20px 0;'>
                
                <div style='background: #fff; padding: 15px; border-radius: 5px; border: 1px solid #eee;'>
                    <p><strong>Status ENV (CLOUDINARY_URL):</strong> $statusEnv</p>
                    <p style='font-size: 12px; color: #666;'>
                        Value: " . ($envUrl ? substr($envUrl, 0, 15) . '... (disensor)' : 'KOSONG') . "
                    </p>
                </div>
            </div>
        ";
    } catch (\Exception $e) {
        return "<h2 style='color:red'>ERROR SYSTEM:</h2> " . $e->getMessage();
    }
});