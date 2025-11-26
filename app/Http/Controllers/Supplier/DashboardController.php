<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Procurement;
use App\Models\Inventory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $orders = Procurement::with(['inventory', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('supplier.dashboard', compact('orders'));
    }

    public function cetakStruk($id)
    {
        $procurement = Procurement::with(['inventory', 'user'])->findOrFail($id);
        return view('supplier.struk', compact('procurement'));
    }

    public function confirmPayment($id)
    {
        $procurement = Procurement::findOrFail($id);
        $procurement->update(['status' => 'paid']);
        return back()->with('success', 'Pembayaran dikonfirmasi.');
    }

    // LOGIKA PENGIRIMAN & PERPINDAHAN STOK
    public function kirimBarang(Request $request, $id)
    {
        $request->validate([
            'tanggal_kirim' => 'required|date',
            'nomor_resi' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        $procurement = Procurement::findOrFail($id);
        $inventory = Inventory::find($procurement->inventory_id);

        // Cek apakah Stok Supplier Cukup?
        if ($inventory->stok_supplier < $procurement->jumlah) {
            return back()->with('error', 'Gagal Kirim! Stok di Gudang Supplier tidak cukup. Sisa: ' . $inventory->stok_supplier);
        }

        if ($procurement->status == 'shipped') {
            return back()->with('error', 'Barang sudah dikirim!');
        }

        // 1. Update Status Pengiriman
        $procurement->update([
            'status' => 'shipped',
            'tanggal_kirim' => $request->tanggal_kirim,
            'nomor_resi' => $request->nomor_resi,
            'catatan' => $request->catatan,
        ]);

        // 2. PINDAHKAN STOK (Supplier -> Dapur)
        // INI YANG MEMBUAT STOK SUPPLIER BERKURANG
        $inventory->decrement('stok_supplier', $procurement->jumlah); 
        
        // INI YANG MEMBUAT STOK DAPUR BERTAMBAH
        $inventory->increment('stok', $procurement->jumlah);          

        return back()->with('success', 'Barang dikirim! Stok Supplier berkurang, Stok Dapur bertambah.');
    }
}