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
        // Mengambil data terbaru (latest) agar yang baru diinput muncul di atas
        $items = Item::latest()->get();
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
            'nama'  => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'stok'  => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Max 2MB
        ]);

        try {
            $imageUrl = null;

            // 2. Upload ke Cloudinary
            if ($request->hasFile('image')) {
                // getRealPath() SANGAT PENTING untuk Vercel (Serverless)
                $uploadedFile = cloudinary()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'kantin_items',
                    'transformation' => [
                        'quality' => 'auto', // Optimasi otomatis agar loading cepat
                        'fetch_format' => 'auto'
                    ]
                ]);

                // Ambil URL Secure (HTTPS)
                $imageUrl = $uploadedFile->getSecurePath();
            }

            // 3. Simpan ke Database NeonDB
            Item::create([
                'nama'  => $request->nama,
                'harga' => $request->harga,
                'stok'  => $request->stok,
                'image' => $imageUrl, // Kita simpan link internetnya
            ]);

            return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil ditambahkan!');

        } catch (\Exception $e) {
            // Jika gagal upload, kembalikan user ke form dengan pesan error
            return back()
                ->withInput()
                ->withErrors(['image' => 'Gagal Upload ke Cloudinary. Pastikan koneksi internet stabil. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Menampilkan detail menu (Redirect ke edit untuk mencegah error 500 jika rute show tidak dibuat)
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
            'nama'  => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'stok'  => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Nullable: user boleh tidak ganti gambar
        ]);

        try {
            // Siapkan data update (nama, harga, stok)
            $data = $request->only(['nama', 'harga', 'stok']);

            // Cek jika user mengupload gambar baru
            if ($request->hasFile('image')) {
                // Upload gambar BARU ke Cloudinary
                $uploadedFile = cloudinary()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'kantin_items',
                    'transformation' => [
                        'quality' => 'auto',
                        'fetch_format' => 'auto'
                    ]
                ]);

                // Masukkan link baru ke array data
                $data['image'] = $uploadedFile->getSecurePath();
            }
            // Jika tidak ada gambar baru, $data['image'] tidak diset, jadi gambar lama aman di DB

            // Update database
            $item->update($data);

            return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['image' => 'Gagal Update: ' . $e->getMessage()]);
        }
    }

    /**
     * Menghapus menu
     */
    public function destroy(Item $item)
    {
        // Catatan: Di Vercel/Cloudinary versi simple, kita hanya hapus data di DB.
        // File di Cloudinary akan tetap ada (orphan), tidak masalah untuk proyek skala kecil.
        
        $item->delete();
        
        return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil dihapus!');
    }

    /**
     * Fitur Cetak Laporan (Opsional)
     */
    public function printMenu()
    {
        $items = Item::all();
        $tanggal = date('d-m-Y');
        
        return view('kasir.items.print_menu', compact('items', 'tanggal'));
    }
}