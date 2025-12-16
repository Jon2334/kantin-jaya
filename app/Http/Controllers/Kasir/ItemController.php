<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
// PENTING: Kita pakai library aslinya langsung
use Cloudinary\Cloudinary; 

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::latest()->get();
        return view('kasir.items.index', compact('items'));
    }

    public function create()
    {
        return view('kasir.items.create');
    }

    // --- FUNGSI BANTUAN UNTUK KONEKSI ---
    private function getCloudinaryInstance()
    {
        // 1. Coba ambil dari variable terpisah (Prioritas)
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey    = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');

        // 2. Jika kosong, coba ambil dari URL panjang
        if (empty($cloudName)) {
            $url = env('CLOUDINARY_URL'); // contoh: cloudinary://123:abc@kantin
            if ($url) {
                $parsed = parse_url($url);
                $cloudName = $parsed['host'] ?? null;
                $apiKey    = $parsed['user'] ?? null;
                $apiSecret = $parsed['pass'] ?? null;
            }
        }

        // 3. Jika masih kosong, matikan proses (Debugging)
        if (empty($cloudName) || empty($apiKey) || empty($apiSecret)) {
            throw new \Exception("Kredensial Cloudinary tidak terbaca dari Vercel! Cek Environment Variables.");
        }

        // 4. Buat objek Cloudinary Native
        return new Cloudinary([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key'    => $apiKey,
                'api_secret' => $apiSecret,
            ],
            'url' => [
                'secure' => true 
            ]
        ]);
    }

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
                // INSTANSIASI MANUAL
                $cloudinary = $this->getCloudinaryInstance();

                // UPLOAD PURE PHP
                $result = $cloudinary->uploadApi()->upload(
                    $request->file('image')->getRealPath(), // File dari folder temp Vercel
                    [
                        'folder' => 'kantin_items',
                        'resource_type' => 'auto',
                        'quality' => 'auto'
                    ]
                );

                // Ambil URL Secure
                $imageUrl = $result['secure_url']; 
            }

            Item::create([
                'nama'  => $request->nama,
                'harga' => $request->harga,
                'stok'  => $request->stok,
                'image' => $imageUrl,
            ]);

            return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil ditambahkan!');

        } catch (\Exception $e) {
            // Tampilkan pesan error lengkap
            return back()->withInput()->withErrors(['image' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function show(Item $item)
    {
        return redirect()->route('kasir.items.edit', $item->id);
    }

    public function edit(Item $item)
    {
        return view('kasir.items.edit', compact('item'));
    }

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
                // INSTANSIASI MANUAL (Lagi)
                $cloudinary = $this->getCloudinaryInstance();

                $result = $cloudinary->uploadApi()->upload(
                    $request->file('image')->getRealPath(),
                    [
                        'folder' => 'kantin_items',
                        'resource_type' => 'auto',
                        'quality' => 'auto'
                    ]
                );

                $data['image'] = $result['secure_url'];
            }

            $item->update($data);

            return redirect()->route('kasir.items.index')->with('success', 'Menu diperbarui!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['image' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('kasir.items.index')->with('success', 'Menu dihapus!');
    }
    
    public function printMenu()
    {
        $items = Item::all();
        $tanggal = date('d-m-Y');
        return view('kasir.items.print_menu', compact('items', 'tanggal'));
    }
}