<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Item;
use App\Models\Procurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Kasir + Laporan Terintegrasi.
     */
    public function index(Request $request)
    {
        // --- BAGIAN 1: DATA DASHBOARD UTAMA ---
        
        // Hitung Total Pendapatan Bersih (Semua Waktu)
        $pemasukanTotal = Order::where('status', 'done')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->sum('order_details.subtotal');

        $pengeluaranTotal = Procurement::whereIn('status', ['paid', 'shipped'])
            ->sum('total_harga');

        $pendapatanBersih = $pemasukanTotal - $pengeluaranTotal;

        // Statistik Order (Pending/Process/Done)
        $stats = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'done' => Order::where('status', 'done')->count(),
        ];

        // --- BAGIAN 2: DATA LAPORAN & RIWAYAT (FILTERABLE) ---
        
        $filter = $request->input('periode', 'hari_ini'); // Default Hari Ini
        $query = Order::with(['user', 'orderDetails.item'])->orderBy('created_at', 'desc');
        
        $judulLaporan = "Laporan Harian";
        $now = Carbon::now();

        // Filter Query Berdasarkan Pilihan
        switch ($filter) {
            case 'hari_ini':
                $query->whereDate('created_at', $now->today());
                $judulLaporan = "Laporan Harian (" . $now->format('d M Y') . ")";
                break;
            case 'bulan_ini':
                $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                $judulLaporan = "Laporan Bulan " . $now->format('F Y');
                break;
            case 'tahun_ini':
                $query->whereYear('created_at', $now->year);
                $judulLaporan = "Laporan Tahun " . $now->year;
                break;
            case 'semua':
                $judulLaporan = "Semua Riwayat Transaksi";
                break;
        }

        $orders = $query->get();

        // Hitung Omzet Khusus Filter Ini
        $omzetLaporan = 0;
        foreach ($orders as $order) {
            // Hitung subtotal manual dari relasi jika belum ada kolom total di tabel orders
            $subtotal = $order->orderDetails->sum('subtotal');
            
            // Hanya hitung omzet jika status done
            if ($order->status == 'done') {
                $omzetLaporan += $subtotal;
            }
        }

        // --- BAGIAN 3: EKSEKUSI CETAK (JIKA TOMBOL CETAK DITEKAN) ---
        if ($request->has('print')) {
            return view('kasir.laporan.print', [
                'laporan' => $orders,
                'judulLaporan' => $judulLaporan,
                'totalOmzet' => $omzetLaporan
            ]);
        }

        return view('kasir.dashboard', compact(
            'orders', 
            'pendapatanBersih', 
            'pemasukanTotal', 
            'pengeluaranTotal', 
            'stats',
            'filter',
            'judulLaporan',
            'omzetLaporan'
        ));
    }

    public function cetakStruk($id)
    {
        $order = Order::with('orderDetails.item')->findOrFail($id);

        if ($order->status !== 'done') {
            return back()->with('error', 'Pesanan belum selesai dimasak oleh dapur!');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->orderDetails as $detail) {
                $item = Item::lockForUpdate()->find($detail->item_id);
                if ($item && $item->stok >= $detail->qty) {
                    $item->stok -= $detail->qty;
                    $item->save();
                }
            }
        });

        return view('kasir.struk', compact('order'));
    }
}