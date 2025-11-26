<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Bahan Baku Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('dapur.inventory.store') }}" method="POST">
                        @csrf

                        <!-- Nama Barang -->
                        <div class="mb-4">
                            <x-input-label for="nama_barang" :value="__('Nama Barang')" />
                            <x-text-input id="nama_barang" class="block mt-1 w-full" type="text" name="nama_barang" required placeholder="Contoh: Beras, Telur, Minyak" autofocus />
                            <x-input-error :messages="$errors->get('nama_barang')" class="mt-2" />
                        </div>

                        <!-- Stok (Support Desimal) -->
                        <div class="mb-4">
                            <x-input-label for="stok" :value="__('Jumlah Stok Awal')" />
                            <x-text-input id="stok" step="0.01" class="block mt-1 w-full" type="number" name="stok" required />
                            <p class="text-xs text-gray-500 mt-1">Bisa menggunakan angka desimal (contoh: 1.5 atau 0.5).</p>
                            <x-input-error :messages="$errors->get('stok')" class="mt-2" />
                        </div>

                        <!-- Satuan (Fleksibel: Pilih atau Ketik) -->
                        <div class="mb-4">
                            <x-input-label for="satuan" :value="__('Satuan')" />
                            
                            <input list="list_satuan" id="satuan" name="satuan" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Pilih atau ketik satuan baru..." required>
                            
                            <!-- Daftar Saran Satuan -->
                            <datalist id="list_satuan">
                                <option value="Kg">
                                <option value="Liter">
                                <option value="Pcs">
                                <option value="Ikat">
                                <option value="Dus">
                                <option value="Butir">
                                <option value="Kaleng">
                                <option value="Botol">
                                <option value="Gram">
                                <option value="Sachet">
                            </datalist>

                            <p class="text-xs text-gray-500 mt-1">Tips: Ketik manual jika satuan yang diinginkan tidak ada di list.</p>
                            <x-input-error :messages="$errors->get('satuan')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('dapur.inventory.index') }}" class="text-gray-600 hover:text-gray-900 underline mr-4">Batal</a>
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>