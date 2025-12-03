<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Inventory::all();
        return view('supplier.products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255|unique:inventories,nama_barang',
            'satuan' => 'required|string|max:50',
            'stok_supplier' => 'required|numeric|min:0', 
            'harga' => 'required|integer|min:0',
        ]);

        Inventory::create([
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'harga' => $request->harga,
            'stok_supplier' => $request->stok_supplier, 
            'stok' => 0, 
        ]);

        return back()->with('success', 'Barang berhasil ditambahkan ke katalog!');
    }

    // === FUNGSI EDIT (BARU) ===
    public function edit($id)
    {
        $product = Inventory::findOrFail($id);
        return view('supplier.products.edit', compact('product'));
    }

    // === FUNGSI UPDATE (BARU) ===
    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $request->validate([
            // Validasi unique kecuali id ini sendiri
            'nama_barang' => 'required|string|max:255|unique:inventories,nama_barang,'.$id,
            'satuan' => 'required|string|max:50',
            'stok_supplier' => 'required|numeric|min:0', 
            'harga' => 'required|integer|min:0',
        ]);

        $inventory->update([
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'stok_supplier' => $request->stok_supplier,
            'harga' => $request->harga,
        ]);

        return redirect()->route('supplier.products.index')->with('success', 'Data barang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return back()->with('success', 'Barang dihapus dari katalog.');
    }
}