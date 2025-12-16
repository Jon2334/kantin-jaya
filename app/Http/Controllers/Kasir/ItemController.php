<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Cloudinary\Configuration\Configuration; // PENTING: Import ini wajib untuk perbaikan manual

class ItemController extends Controller
{
    /**
     * Konfigurasi Cloudinary Manual (Solusi Anti-Error Vercel)
     * Fungsi ini dipanggil sebelum upload untuk memastikan kredensial terbaca.
     */
    private function forceCloudinaryConfig()
    {
        // 1. Coba ambil dari variable terpisah (Saran Utama)
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey    = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');

        // 2. Jika user hanya punya CLOUDINARY_URL (Fallback), kita pecah manual
        if (!$cloudName && env('CLOUDINARY_URL')) {
            $url = env('CLOUDINARY_URL');
            // Format: cloudinary://API_KEY:API_SECRET@CLOUD_NAME
            // Kita parsing string tersebut:
            $parsed = parse_url($url);
            if ($parsed) {
                $cloudName = $parsed['host']; // Bagian setelah @
                $apiKey    = $parsed['user'];
                $apiSecret = $parsed['pass'];
            }
        }

        // 3. Terapkan konfigurasi ke Instance Cloudinary
        if ($cloudName && $apiKey && $apiSecret) {
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => $cloudName,
                    'api_key'    => $apiKey,
                    'api_secret' => $apiSecret
                ],
                'url' => [
                    'secure' => true
                ]
            ]);
        }
    }

    /**
     * Menampilkan daftar menu
     */
    public function index()
    {
        $items = Item::latest()->get();
        return view('kasir.items.index', compact('items'));
    }

    /**
     * Form tambah menu
     */
    public function create()
    {
        return view('kasir.items.create');
    }

    /**
     * SIMPAN DATA (CREATE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'stok'  => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            $imageUrl = null;

            if ($request->hasFile('image')) {
                // PANGGIL FUNGSI PERBAIKAN DI SINI
                $this->forceCloudinaryConfig();

                // Upload menggunakan getRealPath() (Wajib untuk Vercel)
                $uploadedFile = cloudinary()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'kantin_items',
                    'transformation' => [
                        'quality' => 'auto',
                        'fetch_format' => 'auto'
                    ]
                ]);

                $imageUrl = $uploadedFile->getSecurePath();
            }

            Item::create([
                'nama'  => $request->nama,
                'harga' => $request->harga,
                'stok'  => $request->stok,
                'image' => $imageUrl,
            ]);

            return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil ditambahkan!');

        } catch (\Exception $e) {
            // Tampilkan error detail agar kita tahu salahnya dimana
            return back()
                ->withInput()
                ->withErrors(['image' => 'Gagal Upload: ' . $e->getMessage()]);
        }
    }

    /**
     * Detail menu (Redirect ke edit)
     */
    public function show(Item $item)
    {
        return redirect()->route('kasir.items.edit', $item->id);
    }

    /**
     * Form edit menu
     */
    public function edit(Item $item)
    {
        return view('kasir.items.edit', compact('item'));
    }

    /**
     * UPDATE DATA
     */
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'stok'  => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            $data = $request->only(['nama', 'harga', 'stok']);

            if ($request->hasFile('image')) {
                // PANGGIL FUNGSI PERBAIKAN DI SINI JUGA
                $this->forceCloudinaryConfig();

                $uploadedFile = cloudinary()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'kantin_items',
                    'transformation' => [
                        'quality' => 'auto',
                        'fetch_format' => 'auto'
                    ]
                ]);

                $data['image'] = $uploadedFile->getSecurePath();
            }

            $item->update($data);

            return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['image' => 'Gagal Update: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus menu
     */
    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil dihapus!');
    }

    /**
     * Cetak Laporan
     */
    public function printMenu()
    {
        $items = Item::all();
        $tanggal = date('d-m-Y');
        return view('kasir.items.print_menu', compact('items', 'tanggal'));
    }
}