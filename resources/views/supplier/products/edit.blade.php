<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Perbarui Data Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Edit Barang: {{ $product->nama_barang }}</h3>
                    <a href="{{ route('supplier.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Kembali</a>
                </div>

                <form action="{{ route('supplier.products.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Nama Barang -->
                        <div>
                            <x-input-label>Nama Barang</x-input-label>
                            <x-text-input type="text" name="nama_barang" class="w-full" value="{{ old('nama_barang', $product->nama_barang) }}" required />
                        </div>

                        <!-- Satuan -->
                        <div>
                            <x-input-label>Satuan</x-input-label>
                            <input list="list_satuan" name="satuan" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('satuan', $product->satuan) }}" required>
                            <datalist id="list_satuan">
                                <option value="Kg"><option value="Liter"><option value="Pcs"><option value="Dus"><option value="Ikat">
                            </datalist>
                        </div>

                        <!-- Stok Gudang Supplier -->
                        <div>
                            <x-input-label>Stok Gudang Supplier</x-input-label>
                            <x-text-input type="number" step="0.1" name="stok_supplier" class="w-full bg-yellow-50" value="{{ old('stok_supplier', $product->stok_supplier) }}" required />
                            <p class="text-xs text-gray-500 mt-1">Ubah angka ini jika stok di gudang bertambah/berkurang.</p>
                        </div>

                        <!-- Harga -->
                        <div>
                            <x-input-label>Harga Per Satuan (Rp)</x-input-label>
                            <x-text-input type="number" name="harga" class="w-full" value="{{ old('harga', $product->harga) }}" required />
                        </div>

                        <!-- Tombol Update -->
                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-bold transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>