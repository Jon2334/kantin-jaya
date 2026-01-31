<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item; 

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

        // HANYA JALANKAN PENCARIAN JIKA ROLE ADALAH PEMBELI
        if ($role === 'pembeli') {
            $results = Item::where('nama', 'LIKE', "%{$query}%") 
                           ->get();
        } else {
            // Jika role lain (Kasir/Dapur) mencoba akses URL search manual, kembalikan kosong atau redirect
            return redirect()->back()->with('error', 'Fitur pencarian hanya untuk pembeli.');
        }

        return view('search.results', compact('results', 'query', 'role'));
    }
}