<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item; // Pastikan ini Item (Sesuai file Item.php kamu)

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $role = Auth::user()->role;
        $results = collect(); 

        if (!$query) {
            return redirect()->back();
        }

        // 1. KASIR & PEMBELI: Mencari Menu Makanan
        if ($role === 'kasir' || $role === 'pembeli') {
            // PERBAIKAN: Menggunakan 'nama' (bukan 'name') dan menghapus titik tiga (...)
            $results = Item::where('nama', 'LIKE', "%{$query}%") 
                           ->get();
        }
        
        // 2. DAPUR: (Opsional)
        // elseif ($role === 'dapur') { ... }

        return view('search.results', compact('results', 'query', 'role'));
    }
}