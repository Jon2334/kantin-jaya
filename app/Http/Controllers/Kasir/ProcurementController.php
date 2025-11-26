<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Procurement;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcurementController extends Controller
{
    // ... (method index tetap sama) ...
    public function index()
    {
        $inventories = Inventory::all(); 
        
        $procurements = Procurement::with('inventory')
            ->orderBy('created_at', 'desc')
            ->get();

        $pemasukan = Order::where('status', 'done')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->sum('order_details.subtotal');

        $pengeluaran = Procurement::whereIn('status', ['paid', 'shipped'])
            ->sum('total_harga');

        $saldo = $pemasukan - $pengeluaran;

        return view('kasir.procurement.index', compact('inventories', 'procurements', 'saldo'));
    }

    /**
     * FITUR BARU: CETAK LAPORAN PENGELUARAN
     */
    public function print()
    {
        // Ambil hanya data yang sudah dibayar (paid) atau dikirim (shipped)
        // Karena 'pending' belum mengurangi saldo/uang kas
        $procurements = Procurement::with(['inventory', 'user'])
            ->whereIn('status', ['paid', 'shipped'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPengeluaran = $procurements->sum('total_harga');
        $tanggal = date('d M Y');

        return view('kasir.procurement.print', compact('procurements', 'totalPengeluaran', 'tanggal'));
    }

    // ... (method store & storeNewItem tetap sama) ...
    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'jumlah' => 'required|numeric|min:0.1',
            'total_harga' => 'required|integer', 
        ]);

        $inventory = Inventory::find($request->inventory_id);
        if ($inventory->stok_supplier < $request->jumlah) {
            return back()->with('error', 'GAGAL! Stok Supplier tidak mencukupi.');
        }

        $pemasukan = Order::where('status', 'done')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->sum('order_details.subtotal');

        $pengeluaran = Procurement::whereIn('status', ['paid', 'shipped'])
            ->sum('total_harga');

        $saldoSaatIni = $pemasukan - $pengeluaran;

        if ($saldoSaatIni < $request->total_harga) {
            return back()->with('error', 'GAGAL! Saldo Kasir tidak cukup.');
        }

        Procurement::create([
            'user_id' => Auth::id(),
            'inventory_id' => $request->inventory_id,
            'jumlah' => $request->jumlah,
            'total_harga' => $request->total_harga,
            'status' => 'paid',
        ]);

        return back()->with('success', 'Pembayaran berhasil & Permintaan dikirim ke Supplier!');
    }
}