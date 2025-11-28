<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; // <--- WAJIB IMPORT INI

class ItemController extends Controller
{
    /**
     * Menampilkan daftar menu
     */
    public function index()
    {
        $items = Item::all();
        return view('kasir.items.index', compact('items'));
    }

    /**
     * Menampilkan form tambah menu
     */
    public function create()
    {
        return view('kasir.items.create');
    }

    /**
     * Menyimpan data menu baru ke database & Cloudinary
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imageUrl = null;

        // --- PROSES UPLOAD CLOUDINARY ---
        if ($request->hasFile('image')) {
            // Upload ke Cloudinary folder 'kantin_items'
            $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), [
                'folder' => 'kantin_items'
            ]);
            
            // Ambil URL HTTPS yang aman (https://...)
            $imageUrl = $uploadedFile->getSecurePath();
        }

        Item::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'image' => $imageUrl, // Simpan URL internet, bukan path lokal
        ]);

        return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail menu (Mencegah Error 500)
     */
    public function show(Item $item)
    {
        // Karena tidak ada halaman detail khusus, kita arahkan ke edit saja
        return redirect()->route('kasir.items.edit', $item->id);
    }

    /**
     * Menampilkan form edit menu
     */
    public function edit(Item $item)
    {
        return view('kasir.items.edit', compact('item'));
    }

    /**
     * Mengupdate data menu
     */
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
            // --- UPLOAD CLOUDINARY BARU ---
            $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), [
                'folder' => 'kantin_items'
            ]);

            // Ganti link gambar di database dengan link baru dari Cloudinary
            $data['image'] = $uploadedFile->getSecurePath();
        }

        $item->update($data);

        return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Menghapus menu
     */
    public function destroy(Item $item)
    {
        // KHUSUS VERCEL: Jangan gunakan Storage::delete() untuk file lokal.
        // File di Cloudinary biarkan saja (atau hapus lewat dashboard jika penuh).
        // Kita cukup hapus data di database agar tidak error "Read-only file system".

        $item->delete();
        
        return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil dihapus!');
    }

    /**
     * Fitur Cetak Laporan
     */
    public function printMenu()
    {
        $items = Item::all();
        $tanggal = date('d-m-Y');
        
        return view('kasir.items.print_menu', compact('items', 'tanggal'));
    }
}