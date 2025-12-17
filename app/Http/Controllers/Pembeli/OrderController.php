<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Menyimpan Pesanan Baru.
     * Logika TERBARU (Potong Awal):
     * 1. Filter input.
     * 2. Validasi stok & Lock data (mencegah rebutan).
     * 3. Buat Order & OrderDetail.
     * 4. LANGSUNG KURANGI STOK di Database.
     */
    public function store(Request $request)
    {
        // Validasi metode pembayaran
        $request->validate([
            'payment_method' => 'required|in:qris,tunai',
            'qty' => 'required|array', 
        ]);

        // 1. Filter keranjang: Ambil hanya item yang jumlah pesannya > 0
        $cart = array_filter($request->qty, function ($quantity) {
            return $quantity > 0;
        });

        if (empty($cart)) {
            return back()->with('error', 'Silakan pilih minimal satu menu!');
        }

        // 2. Mulai Database Transaction (PENTING AGAR DATA KONSISTEN)
        DB::beginTransaction();

        try {
            // A. Buat Data Order (Kepala Transaksi)
            $order = Order::create([
                'user_id' => Auth::id(),
                'payment_method' => $request->payment_method,
                'status' => 'pending', 
            ]);

            // B. Loop setiap item
            foreach ($cart as $itemId => $qty) {
                
                // PENTING: Gunakan lockForUpdate()
                // Ini mencegah dua orang membeli item terakhir secara bersamaan
                $itemDb = Item::lockForUpdate()->find($itemId);

                // Cek Validasi Item & Stok
                if (!$itemDb) {
                    throw new \Exception("Item dengan ID $itemId tidak ditemukan.");
                }

                if ($itemDb->stok < $qty) {
                    // Jika stok kurang, batalkan seluruh pesanan
                    throw new \Exception("Stok menu '{$itemDb->nama}' tidak cukup! Sisa: {$itemDb->stok}");
                }

                // C. Buat Order Detail
                OrderDetail::create([
                    'order_id' => $order->id,
                    'item_id' => $itemDb->id,
                    'qty' => $qty,
                    'harga_satuan' => $itemDb->harga,
                    'subtotal' => $itemDb->harga * $qty,
                ]);
                
                // D. --- LOGIKA POTONG STOK DI AWAL ---
                // Karena stok sudah divalidasi cukup, kita kurangi sekarang juga.
                $itemDb->stok = $itemDb->stok - $qty;
                $itemDb->save();
            }

            // Jika semua lancar, simpan permanen
            DB::commit();

            return redirect()->route('pembeli.dashboard')
                ->with('success', 'Pesanan berhasil! Stok telah diamankan untuk Anda.');

        } catch (\Exception $e) {
            // Jika error, kembalikan stok seperti semula (Rollback)
            DB::rollback();
            return back()->with('error', 'Gagal memesan: ' . $e->getMessage());
        }
    }
}