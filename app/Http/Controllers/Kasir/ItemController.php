<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

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
        // 1. Validasi Input
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $imageUrl = null;

            // 2. Cek apakah ada file gambar yang diupload
            if ($request->hasFile('image')) {
                
                // --- UPLOAD KE CLOUDINARY (Cara Stabil) ---
                // Kita gunakan fungsi helper cloudinary() langsung
                $uploadedFile = cloudinary()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'kantin_items'
                ]);
                
                // Ambil URL aman (https)
                $imageUrl = $uploadedFile->getSecurePath();
            }

            // 3. Simpan ke Database Neon
            Item::create([
                'nama' => $request->nama,
                'harga' => $request->harga,
                'stok' => $request->stok,
                'image' => $imageUrl, // Simpan link internet
            ]);

            return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil ditambahkan!');

        } catch (\Exception $e) {
            // Jika Error (misal Cloudinary putus), kembalikan ke form dengan pesan jelas
            return back()
                ->withInput()
                ->withErrors(['image' => 'Gagal Upload: ' . $e->getMessage() . '. Cek CLOUDINARY_URL di Vercel!']);
        }
    }

    /**
     * Menampilkan detail menu (PENTING: Mencegah Error 500 jika diklik)
     */
    public function show(Item $item)
    {
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

        try {
            $data = $request->only(['nama', 'harga', 'stok']);

            if ($request->hasFile('image')) {
                // --- UPLOAD KE CLOUDINARY (Update) ---
                $uploadedFile = cloudinary()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'kantin_items'
                ]);

                // Ganti link lama dengan link baru
                $data['image'] = $uploadedFile->getSecurePath();
            }

            $item->update($data);

            return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['image' => 'Gagal Update: ' . $e->getMessage()]);
        }
    }

    /**
     * Menghapus menu
     */
    public function destroy(Item $item)
    {
        // KHUSUS VERCEL: 
        // Jangan pakai Storage::delete() karena Vercel Read-Only.
        // Cukup hapus data di database saja.
        
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