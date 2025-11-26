<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update Stok: ') }} {{ $inventory->nama_barang }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('dapur.inventory.update', $inventory->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Nama Barang -->
                        <div class="mb-4">
                            <x-input-label for="nama_barang" :value="__('Nama Barang')" />
                            <x-text-input id="nama_barang" class="block mt-1 w-full" type="text" name="nama_barang" value="{{ $inventory->nama_barang }}" required />
                        </div>

                        <!-- Stok (Support Desimal) -->
                        <div class="mb-4">
                            <x-input-label for="stok" :value="__('Jumlah Stok')" />
                            <x-text-input id="stok" step="0.01" class="block mt-1 w-full" type="number" name="stok" value="{{ $inventory->stok }}" required />
                            <p class="text-xs text-gray-500 mt-1">Update angka ini sesuai sisa stok riil di dapur. Bisa desimal (contoh: 2.5).</p>
                        </div>

                        <!-- Satuan (Fleksibel) -->
                        <div class="mb-4">
                            <x-input-label for="satuan" :value="__('Satuan')" />
                            
                            <input list="list_satuan" id="satuan" name="satuan" value="{{ $inventory->satuan }}" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Pilih atau ketik satuan baru..." required>
                            
                            <!-- Daftar Saran -->
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
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('dapur.inventory.index') }}" class="text-gray-600 hover:text-gray-900 underline mr-4">Batal</a>
                            <x-primary-button>{{ __('Update Data') }}</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>