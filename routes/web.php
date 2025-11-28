<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;     // <--- TAMBAHAN: Wajib untuk Vercel
use Illuminate\Support\Facades\Artisan; // <--- TAMBAHAN: Wajib untuk route penyelamat
use App\Http\Controllers\ProfileController;

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
if (env('APP_ENV') === 'production') {
    URL::forceScheme('https');
}

// 1. Route Utama (Redirect Otomatis)
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
    
    // Kelola Stok Menu
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
    // KDS
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
// 7. ROUTE PENYELAMAT (FIX CONFIG CLOUDINARY)
// =========================================================================
Route::get('/fix-cloudinary', function () {
    try {
        // 1. Bersihkan semua cache yang nyangkut
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        
        // 2. Cek apakah ENV Cloudinary terbaca
        $url = env('CLOUDINARY_URL');
        $status = empty($url) ? '<span style="color:red; font-weight:bold;">GAGAL / KOSONG</span>' : '<span style="color:green; font-weight:bold;">AMAN / TERBACA</span>';
        
        return "
            <div style='font-family: sans-serif; padding: 20px;'>
                <h1>Status Konfigurasi Vercel</h1>
                <p>Status Cache: <strong>BERHASIL DIBERSIHKAN</strong></p>
                <hr>
                <p>Status CLOUDINARY_URL: $status</p>
                <p>Isi Variable (Disensor): " . ($url ? substr($url, 0, 25) . '*****************' : 'NULL') . "</p>
                <br>
                <div style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>
                    <strong>Panduan:</strong><br>
                    1. Jika status <b>AMAN</b>, silakan coba upload foto menu sekarang.<br>
                    2. Jika status <b>GAGAL</b>, berarti kamu salah input di Settings Vercel (Environment Variables).
                </div>
                <br>
                <a href='/' style='background: blue; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Kembali ke Home</a>
            </div>
        ";
    } catch (\Exception $e) {
        return "ERROR: " . $e->getMessage();
    }
});