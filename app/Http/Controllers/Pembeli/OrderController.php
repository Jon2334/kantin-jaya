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
     * Logika:
     * 1. Filter input (hanya ambil item yang qty > 0).
     * 2. Validasi stok (Cek apakah stok di DB cukup untuk jumlah yg dipesan).
     * 3. Jika cukup -> Buat Order & OrderDetail.
     * 4. Jika tidak -> Batalkan transaksi & beri pesan error.
     */
    public function store(Request $request)
    {
        // Validasi metode pembayaran
        $request->validate([
            'payment_method' => 'required|in:qris,tunai',
            'qty' => 'required|array', // Array dari input number: name="qty[item_id]"
        ]);

        // 1. Filter keranjang: Ambil hanya item yang jumlah pesannya > 0
        // $request->qty bentuknya: [item_id => jumlah, item_id => jumlah]
        $cart = array_filter($request->qty, function ($quantity) {
            return $quantity > 0;
        });

        // Jika user klik pesan tapi semua qty 0
        if (empty($cart)) {
            return back()->with('error', 'Silakan pilih minimal satu menu!');
        }

        // 2. Mulai Database Transaction
        // Menggunakan try-catch agar bisa di-rollback jika stok habis
        DB::beginTransaction();

        try {
            // Buat Data Order (Kepala Transaksi)
            $order = Order::create([
                'user_id' => Auth::id(),
                'payment_method' => $request->payment_method,
                'status' => 'pending', // Default masuk ke dapur sebagai pending
            ]);

            // Loop setiap item yang ada di keranjang
            foreach ($cart as $itemId => $qty) {
                // Ambil data item terbaru dari DB (gunakan lockForUpdate untuk mencegah race condition)
                $itemDb = Item::lockForUpdate()->find($itemId);

                // Cek Validasi Stok
                if (!$itemDb) {
                    throw new \Exception("Item dengan ID $itemId tidak ditemukan.");
                }

                if ($itemDb->stok < $qty) {
                    // Jika stok kurang, lempar error (akan ditangkap catch)
                    throw new \Exception("Stok untuk menu '{$itemDb->nama}' tidak mencukupi! Sisa: {$itemDb->stok}");
                }

                // Jika Stok Aman, Buat Order Detail
                OrderDetail::create([
                    'order_id' => $order->id,
                    'item_id' => $itemDb->id,
                    'qty' => $qty,
                    'harga_satuan' => $itemDb->harga,
                    'subtotal' => $itemDb->harga * $qty,
                ]);
                
                // CATATAN: Sesuai flowchart Anda, stok baru dikurangi saat KASIR mencetak struk/selesai.
                // Jadi di sini kita HANYA validasi ketersediaan, belum mengurangi kolom stok di tabel item.
            }

            // Jika semua lancar, simpan permanen
            DB::commit();

            return redirect()->route('pembeli.dashboard')
                ->with('success', 'Pesanan berhasil dibuat! Mohon tunggu konfirmasi dapur.');

        } catch (\Exception $e) {
            // Jika ada error (stok habis dll), batalkan semua perubahan
            DB::rollback();
            return back()->with('error', 'Gagal memesan: ' . $e->getMessage());
        }
    }
}