<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage; // Tidak dipakai lagi di Vercel
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; // <--- WAJIB: Import Cloudinary

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return view('kasir.items.index', compact('items'));
    }

    public function create()
    {
        return view('kasir.items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imageUrl = null;

        if ($request->hasFile('image')) {
            // --- UBAHAN KHUSUS VERCEL (CLOUDINARY) ---
            // Upload file ke Cloudinary dan ambil URL HTTPS-nya yang aman
            $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), [
                'folder' => 'kantin_items' // Nama folder di Cloudinary (opsional)
            ]);
            
            $imageUrl = $uploadedFile->getSecurePath();
        }

        Item::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'image' => $imageUrl, // Simpan URL Cloudinary (https://...), bukan path lokal
        ]);

        return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function edit(Item $item)
    {
        return view('kasir.items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['nama', 'harga', 'stok']);

        if ($request->hasFile('image')) {
            // --- UBAHAN KHUSUS VERCEL (CLOUDINARY) ---
            // Kita langsung timpa dengan gambar baru. 
            // (Menghapus gambar lama di Cloudinary agak rumit, untuk pemula cukup upload baru saja)
            
            $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), [
                'folder' => 'kantin_items'
            ]);

            $data['image'] = $uploadedFile->getSecurePath();
        }

        $item->update($data);

        return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy(Item $item)
    {
        // --- UBAHAN KHUSUS VERCEL ---
        // Kita matikan fitur delete file fisik, karena Vercel Read-Only.
        // Hapus data di database saja sudah cukup agar tidak error.
        
        /* if ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }
        */

        $item->delete();
        return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil dihapus!');
    }

    public function printMenu()
    {
        $items = Item::all();
        $tanggal = date('d-m-Y');
        
        return view('kasir.items.print_menu', compact('items', 'tanggal'));
    }
}