<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Katalog Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- BAGIAN NOTIFIKASI DIHAPUS KARENA SUDAH ADA DI LAYOUT UTAMA -->

            <!-- FORM TAMBAH MASTER BARANG -->
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Tambah Barang Baru ke Katalog</h3>
                </div>

                <form action="{{ route('supplier.products.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <!-- Nama Barang -->
                        <div class="md:col-span-2">
                            <x-input-label>Nama Barang</x-input-label>
                            <x-text-input type="text" name="nama_barang" class="w-full" required placeholder="Contoh: Daging Sapi Premium" />
                        </div>

                        <!-- Satuan -->
                        <div>
                            <x-input-label>Satuan</x-input-label>
                            <input list="list_satuan" name="satuan" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Pilih..." required>
                            <datalist id="list_satuan">
                                <option value="Kg"><option value="Liter"><option value="Pcs"><option value="Dus"><option value="Ikat"><option value="Butir"><option value="Kaleng">
                            </datalist>
                        </div>

                        <!-- Stok Awal -->
                        <div>
                            <x-input-label>Stok Awal</x-input-label>
                            <x-text-input type="number" step="0.1" name="stok_supplier" class="w-full" required placeholder="0" />
                        </div>

                        <!-- Harga Satuan -->
                        <div class="md:col-span-2">
                            <x-input-label>Harga Per Satuan (Rp)</x-input-label>
                            <x-text-input type="number" name="harga" class="w-full" required placeholder="Contoh: 15000" />
                        </div>

                        <!-- Tombol Simpan -->
                        <div class="md:col-span-2">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-bold h-10 transition">
                                + Tambah ke Katalog
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- TABEL DAFTAR BARANG -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold mb-4 text-gray-800">Daftar Barang Tersedia</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Gudang Supplier</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($products as $index => $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-bold text-gray-800">{{ $item->nama_barang }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $item->satuan }}</td>
                                    <td class="px-4 py-3 text-sm font-mono font-bold text-green-600">
                                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-3 py-1 rounded-full font-mono font-bold text-white {{ $item->stok_supplier > 10 ? 'bg-blue-500' : 'bg-red-500' }}">
                                            {{ (float)$item->stok_supplier }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center flex justify-center space-x-3">
                                        <!-- TOMBOL EDIT -->
                                        <a href="{{ route('supplier.products.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs underline">
                                            Perbarui
                                        </a>

                                        <!-- TOMBOL HAPUS -->
                                        <form action="{{ route('supplier.products.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini dari katalog?');">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-xs underline">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">
                                        Belum ada barang di katalog. Silakan tambahkan barang baru di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>