<?php

namespace App\Http\Controllers\Dapur;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Menampilkan Daftar Stok Bahan Baku.
     * HANYA TAMPILKAN YANG ADA STOKNYA DI DAPUR
     */
    public function index()
    {
        // Tambahkan where('stok', '>', 0)
        // Agar barang yang masih di supplier (stok dapur 0) tidak muncul.
        $inventories = Inventory::where('stok', '>', 0)->get();
        
        return view('dapur.inventory.index', compact('inventories'));
    }

    /**
     * FITUR CETAK LAPORAN
     */
    public function print()
    {
        // Print juga hanya yang ada stoknya
        $inventories = Inventory::where('stok', '>', 0)->get();
        $tanggal = date('d-m-Y');
        
        return view('dapur.inventory.print', compact('inventories', 'tanggal'));
    }

    // ... (Method create, store, edit, update, destroy, kurangiStok TETAP SAMA seperti sebelumnya) ...
    
    public function create()
    {
        abort(403, 'Akses Ditolak: Fitur Tambah Barang hanya untuk Kasir.');
    }

    public function store(Request $request)
    {
        abort(403, 'Akses Ditolak: Fitur Tambah Barang hanya untuk Kasir.');
    }

    public function edit($id)
    {
        abort(403, 'Akses Ditolak: Fitur Edit Stok telah dinonaktifkan.');
    }

    public function update(Request $request, $id)
    {
        abort(403, 'Akses Ditolak: Fitur Update Stok telah dinonaktifkan.');
    }

    public function destroy($id)
    {
        abort(403, 'Akses Ditolak: Fitur Hapus Stok telah dinonaktifkan.');
    }

    public function kurangiStok(Request $request, $id)
    {
        $request->validate([
            'jumlah_pakai' => 'required|numeric|min:0.01',
        ]);

        $inventory = Inventory::findOrFail($id);

        if ($inventory->stok < $request->jumlah_pakai) {
            return back()->with('error', 'Stok tidak cukup! Sisa stok hanya: ' . (float)$inventory->stok);
        }

        $inventory->decrement('stok', $request->jumlah_pakai);

        return back()->with('success', 'Stok berhasil dikurangi sebanyak ' . $request->jumlah_pakai . ' ' . $inventory->satuan);
    }
}