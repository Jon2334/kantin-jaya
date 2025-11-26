<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Pembeli.
     * Halaman ini berisi:
     * 1. Daftar Menu (Item) yang bisa dipesan.
     * 2. Riwayat pesanan user tersebut (untuk memantau status).
     */
    public function index()
    {
        // 1. Ambil semua data menu
        // Kita tampilkan semua, nanti di View tombol pesan didisable jika stok 0
        $items = Item::all();

        // 2. Ambil riwayat pesanan milik user yang sedang login
        // Diurutkan dari yang terbaru
        $riwayatPesanan = Order::with('orderDetails.item')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pembeli.dashboard', compact('items', 'riwayatPesanan'));
    }
}