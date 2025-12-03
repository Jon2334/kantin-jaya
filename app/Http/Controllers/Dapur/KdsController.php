<?php

namespace App\Http\Controllers\Dapur;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Inventory;
use Illuminate\Http\Request;

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

        $request->validate([
            'status' => 'required|in:processing,done',
            'bahan'  => 'nullable|array', 
        ]);

        // --- LOGIKA PENGURANGAN STOK ---
        if ($request->status == 'done' && $request->has('bahan') && is_array($request->bahan)) {
            
            foreach ($request->bahan as $inventoryId => $data) {
                // Ambil input, pastikan float
                $jumlahPakai = isset($data['jumlah']) ? (float) $data['jumlah'] : 0;
                // Bersihkan input satuan (kecilkan huruf & hapus spasi)
                $satuanPakai = isset($data['satuan']) ? strtolower(trim($data['satuan'])) : ''; 

                if ($jumlahPakai > 0) {
                    $inventory = Inventory::find($inventoryId);
                    
                    if ($inventory) {
                        $stokAsli = (float) $inventory->stok;
                        // Bersihkan satuan database juga
                        $satuanAsli = strtolower(trim($inventory->satuan)); 
                        $pengurang = 0;

                        // === LOGIKA KONVERSI AMAN ===
                        // 1. Konversi Gram ke Kg
                        if (($satuanAsli == 'kg' || $satuanAsli == 'kilogram') && $satuanPakai == 'gram') {
                            $pengurang = $jumlahPakai / 1000; 
                        }
                        // 2. Konversi ml ke Liter
                        elseif (($satuanAsli == 'liter' || $satuanAsli == 'l') && $satuanPakai == 'ml') {
                            $pengurang = $jumlahPakai / 1000;
                        }
                        // 3. Satuan Sama / Tidak Perlu Konversi
                        else {
                            $pengurang = $jumlahPakai;
                        }

                        // === EKSEKUSI ===
                        // Gunakan float comparison agar akurat
                        if ($stokAsli >= $pengurang) {
                            $inventory->decrement('stok', $pengurang);
                        } else {
                            // Jika stok kurang (misal sisa 0.1 tapi mau kurang 0.2), habiskan saja jadi 0
                            $inventory->update(['stok' => 0]);
                        }
                    }
                }
            }
        }

        $order->update(['status' => $request->status]);

        $pesan = ($request->status == 'done') 
            ? 'Pesanan selesai & Stok dikurangi!' 
            : 'Pesanan diproses.';

        return redirect()->back()->with('success', $pesan);
    }
}