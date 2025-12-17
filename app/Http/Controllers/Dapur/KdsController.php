<?php

namespace App\Http\Controllers\Dapur;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk Transaction

class KdsController extends Controller
{
    public function index()
    {
        $orders = Order::with(['orderDetails.item', 'user'])
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Pastikan hanya ambil inventory yang stok dapurnya > 0
        $inventories = Inventory::where('stok', '>', 0)->get(); 

        return view('dapur.dashboard', compact('orders', 'inventories'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // --- [FIX UTAMA] CEGAH DOUBLE DEDUCTION ---
        // Jika status di database sudah 'done', jangan lakukan apa-apa lagi.
        if ($order->status == 'done') {
            return redirect()->back()->with('error', 'Pesanan ini sudah diselesaikan sebelumnya.');
        }

        $request->validate([
            'status' => 'required|in:processing,done',
            'bahan'  => 'nullable|array', 
        ]);

        // Gunakan DB Transaction agar atomik (semua sukses atau semua gagal)
        try {
            DB::transaction(function () use ($order, $request) {
                
                // --- LOGIKA PENGURANGAN STOK ---
                // Hanya jalankan jika status baru adalah 'done'
                if ($request->status == 'done' && $request->has('bahan') && is_array($request->bahan)) {
                    
                    foreach ($request->bahan as $inventoryId => $data) {
                        $jumlahPakai = isset($data['jumlah']) ? (float) $data['jumlah'] : 0;
                        $satuanPakai = isset($data['satuan']) ? strtolower(trim($data['satuan'])) : ''; 

                        if ($jumlahPakai > 0) {
                            // Lock baris inventory agar tidak bentrok (optional tapi bagus untuk high traffic)
                            $inventory = Inventory::where('id', $inventoryId)->lockForUpdate()->first();
                            
                            if ($inventory) {
                                $stokAsli = (float) $inventory->stok;
                                $satuanAsli = strtolower(trim($inventory->satuan)); 
                                $pengurang = 0;

                                // === LOGIKA KONVERSI AMAN ===
                                if (($satuanAsli == 'kg' || $satuanAsli == 'kilogram') && $satuanPakai == 'gram') {
                                    $pengurang = $jumlahPakai / 1000; 
                                }
                                elseif (($satuanAsli == 'liter' || $satuanAsli == 'l') && $satuanPakai == 'ml') {
                                    $pengurang = $jumlahPakai / 1000;
                                }
                                else {
                                    $pengurang = $jumlahPakai;
                                }

                                // === EKSEKUSI PENGURANGAN ===
                                if ($stokAsli >= $pengurang) {
                                    $inventory->decrement('stok', $pengurang);
                                } else {
                                    $inventory->update(['stok' => 0]);
                                }
                            }
                        }
                    }
                }

                // Update status order setelah stok aman
                $order->update(['status' => $request->status]);
            });

        } catch (\Exception $e) {
            // Jika terjadi error saat transaksi
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        $pesan = ($request->status == 'done') 
            ? 'Pesanan selesai & Stok berhasil dikurangi!' 
            : 'Pesanan diproses.';

        return redirect()->back()->with('success', $pesan);
    }
}