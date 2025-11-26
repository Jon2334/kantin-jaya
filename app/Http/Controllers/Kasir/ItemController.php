<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return view('kasir.items.index', compact('items'));
    }

    // ... (method create, store, edit, update, destroy tetap sama) ...

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

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }

        Item::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'image' => $imagePath,
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
            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        $item->update($data);

        return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy(Item $item)
    {
        if ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();
        return redirect()->route('kasir.items.index')->with('success', 'Menu berhasil dihapus!');
    }

    /**
     * FITUR BARU: CETAK LAPORAN MENU
     */
    public function printMenu()
    {
        $items = Item::all();
        $tanggal = date('d-m-Y');
        
        return view('kasir.items.print_menu', compact('items', 'tanggal'));
    }
}