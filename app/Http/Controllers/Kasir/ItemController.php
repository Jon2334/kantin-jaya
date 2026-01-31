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

    // --- FUNGSI BANTUAN UNTUK KONEKSI CLOUDINARY ---
    private function getCloudinaryInstance()
    {
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey    = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');

        // Jika env variabel kosong, coba ambil dari CLOUDINARY_URL
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
            throw new \Exception("Kredensial Cloudinary bermasalah. Cek file .env Anda.");
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
        // 1. Validasi
        $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string', // Tambahkan validasi deskripsi
            'harga'     => 'required|integer|min:0',
            'stok'      => 'required|integer|min:0',
            'image'     => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            $imageUrl = null;

            // 2. Upload Gambar
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

            // 3. Simpan ke Database
            Item::create([
                'nama'      => $request->nama,
                'deskripsi' => $request->deskripsi, // Simpan deskripsi
                'harga'     => $request->harga,
                'stok'      => $request->stok,
                'image'     => $imageUrl,
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
        // 1. Validasi
        $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string', // Validasi deskripsi saat edit
            'harga'     => 'required|integer|min:0',
            'stok'      => 'required|integer|min:0',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            // 2. Siapkan Data Update
            $data = [
                'nama'      => $request->nama,
                'deskripsi' => $request->deskripsi, // Masukkan deskripsi baru
                'harga'     => $request->harga,
                'stok'      => $request->stok,
            ];

            // 3. Cek Jika Ada Gambar Baru
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

            // 4. Update Database
            $item->update($data);
            
            return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['image' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('kasir.items.index')->with('success', 'Menu dihapus!');
    }
    
    // --- FUNGSI CETAK LAPORAN ---
    public function printMenu()
    {
        $items = Item::all();
        $tanggal = date('d-m-Y');

        // Proses konversi gambar ke Base64 agar bisa dicetak offline/PDF
        foreach ($items as $item) {
            $item->base64_image = null; 

            if (!empty($item->image)) {
                try {
                    // Cek jika URL Online (Cloudinary)
                    if (filter_var($item->image, FILTER_VALIDATE_URL)) {
                        $arrContextOptions = [
                            "ssl" => [
                                "verify_peer" => false,
                                "verify_peer_name" => false,
                            ],
                        ];
                        
                        $imageContent = file_get_contents($item->image, false, stream_context_create($arrContextOptions));
                        
                        if ($imageContent !== false) {
                            $base64 = base64_encode($imageContent);
                            $item->base64_image = 'data:image/jpeg;base64,' . $base64; 
                        }
                    }
                    // Cek jika File Lokal
                    elseif (file_exists(public_path($item->image))) {
                        $path = public_path($item->image);
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $item->base64_image = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                } catch (\Exception $e) {
                    $item->base64_image = null;
                }
            }
        }

        return view('kasir.items.print_menu', compact('items', 'tanggal'));
    }
}