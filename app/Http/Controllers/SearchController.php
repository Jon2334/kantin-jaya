<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller; // Tambahkan ini agar aman
use App\Models\Item; // PASTEKAN NAMA FILE MODEL KAMU (Cek folder app/Models)

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $role = Auth::user()->role;
        $results = collect(); // Default koleksi kosong

        // Validasi input agar tidak error jika kosong
        if (!$query) {
            return redirect()->back();
        }

        // LOGIKA PENCARIAN BERDASARKAN ROLE
        // 1. KASIR & PEMBELI: Mencari Menu Makanan
        if ($role === 'kasir' || $role === 'pembeli') {
            // Perbaikan baris 27 yang error sebelumnya
            $results = Item::where('name', 'LIKE', "%{$query}%")
                           ->orWhere('description', 'LIKE', "%{$query}%")
                           ->get();
        }
        
        // 2. DAPUR: Mencari Stok Bahan (Jika diperlukan)
        // elseif ($role === 'dapur') {
        //      $results = \App\Models\Inventory::where('nama_bahan', 'LIKE', "%{$query}%")->get();
        // }

        return view('search.results', compact('results', 'query', 'role'));
    }
}