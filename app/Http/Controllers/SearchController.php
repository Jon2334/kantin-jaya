<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Pastikan Model Item/Produk kamu benar. Saya asumsikan namanya 'Item' berdasarkan ItemController
use App\Models\Item; 

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $role = Auth::user()->role;
        $results = collect(); // Koleksi kosong default

        // Validasi input agar tidak error jika kosong
        if (!$query) {
            return redirect()->back();
        }

        // LOGIKA PENCARIAN BERDASARKAN ROLE
        
        // 1. KASIR & PEMBELI: Mencari Menu Makanan
        if ($role === 'kasir' || $role === 'pembeli') {
            $results = Item::where('name', 'LIKE', "%{$query}%")
                           ->orWhere('description', 'LIKE', "%{$query}%")
                           ->get();
        }
        
        // 2. DAPUR: Mungkin mencari bahan baku (Opsional, sesuaikan model Inventory kamu)
        // elseif ($role === 'dapur') {
        //      $results = \App\Models\Inventory::where('nama_bahan', 'LIKE', "%{$query}%")->get();
        // }

        return view('search.results', compact('results', 'query', 'role'));
    }
}