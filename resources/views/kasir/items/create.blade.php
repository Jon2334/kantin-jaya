<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Menu Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Perhatikan enctype ini! Wajib ada untuk upload file -->
                    <form action="{{ route('kasir.items.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Gambar Menu -->
                        <div class="mb-4">
                            <x-input-label for="image" :value="__('Foto Makanan')" />
                            <input id="image" class="block mt-1 w-full border border-gray-300 rounded p-2" type="file" name="image" required accept="image/*" />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <!-- Nama Menu -->
                        <div class="mb-4">
                            <x-input-label for="nama" :value="__('Nama Menu')" />
                            <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama')" required />
                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                        </div>

                        <!-- Harga -->
                        <div class="mb-4">
                            <x-input-label for="harga" :value="__('Harga (Rupiah)')" />
                            <x-text-input id="harga" class="block mt-1 w-full" type="number" name="harga" :value="old('harga')" required />
                            <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                        </div>

                        <!-- Stok -->
                        <div class="mb-4">
                            <x-input-label for="stok" :value="__('Stok Awal')" />
                            <x-text-input id="stok" class="block mt-1 w-full" type="number" name="stok" :value="old('stok')" required />
                            <x-input-error :messages="$errors->get('stok')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('kasir.items.index') }}" class="text-gray-600 hover:text-gray-900 underline mr-4">Batal</a>
                            <x-primary-button>{{ __('Simpan Menu') }}</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>