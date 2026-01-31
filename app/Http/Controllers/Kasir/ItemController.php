<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
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
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey    = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');

        if (empty($cloudName)) {
            $url = env('CLOUDINARY_URL');
            if ($url) {
                $parsed = parse_url($url);
                $cloudName = $parsed['host'] ?? null;
                $apiKey    = $parsed['user'] ?? null;
                $apiSecret = $parsed['pass'] ?? null;
            }
        }

        if (empty($cloudName) || empty($apiKey) || empty($apiSecret)) {
            throw new \Exception("Kredensial Cloudinary bermasalah.");
        }

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
                $cloudinary = $this->getCloudinaryInstance();
                $result = $cloudinary->uploadApi()->upload(
                    $request->file('image')->getRealPath(),
                    [
                        'folder' => 'kantin_items',
                        'resource_type' => 'auto',
                        'quality' => 'auto'
                    ]
                );
                $imageUrl = $result['secure_url']; 
            }

            Item::create([
                'nama'  => $request->nama,
                'deskripsi' => $request->deskripsi,
                'harga' => $request->harga,
                'stok'  => $request->stok,
                'image' => $imageUrl,
            ]);

            return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil ditambahkan!');

        } catch (\Exception $e) {
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
    
    // --- PERBAIKAN DI FUNGSI INI ---
    public function printMenu()
    {
        $items = Item::all();
        $tanggal = date('d-m-Y');

        // Loop setiap item untuk mengubah URL Cloudinary menjadi Base64
        // Agar bisa dibaca oleh PDF generator atau Printer tanpa error SSL
        foreach ($items as $item) {
            // Siapkan property baru sementara
            $item->base64_image = null; 

            if (!empty($item->image)) {
                try {
                    // Cek 1: Apakah ini URL Online (Cloudinary)
                    if (filter_var($item->image, FILTER_VALIDATE_URL)) {
                        // Setup context untuk bypass SSL (jaga-jaga error sertifikat)
                        $arrContextOptions = [
                            "ssl" => [
                                "verify_peer" => false,
                                "verify_peer_name" => false,
                            ],
                        ];
                        
                        // Download gambar ke memory server sementara
                        $imageContent = file_get_contents($item->image, false, stream_context_create($arrContextOptions));
                        
                        if ($imageContent !== false) {
                            $base64 = base64_encode($imageContent);
                            // Set string base64 yang siap pakai di <img src>
                            $item->base64_image = 'data:image/jpeg;base64,' . $base64; 
                        }
                    }
                    // Cek 2: Apakah ini file Local (jaga-jaga ada data lama bukan Cloudinary)
                    elseif (file_exists(public_path($item->image))) {
                        $path = public_path($item->image);
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $item->base64_image = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                } catch (\Exception $e) {
                    // Jika gagal download gambar, biarkan null agar tidak error seluruh halaman
                    $item->base64_image = null;
                }
            }
        }

        return view('kasir.items.print_menu', compact('items', 'tanggal'));
    }
}