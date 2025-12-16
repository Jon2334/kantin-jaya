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
                    
                    <form id="uploadForm" action="{{ route('kasir.items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="image" :value="__('Foto Makanan')" />
                            <input id="image" 
                                   class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2" 
                                   type="file" 
                                   name="image" 
                                   required 
                                   accept="image/*" />
                            <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, WEBP. Maks: 2MB.</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="nama" :value="__('Nama Menu')" />
                            <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama')" required placeholder="Contoh: Nasi Goreng Spesial" />
                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="harga" :value="__('Harga (Rupiah)')" />
                            <x-text-input id="harga" class="block mt-1 w-full" type="number" name="harga" :value="old('harga')" required min="0" placeholder="Contoh: 15000" />
                            <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="stok" :value="__('Stok Awal')" />
                            <x-text-input id="stok" class="block mt-1 w-full" type="number" name="stok" :value="old('stok')" required min="0" placeholder="Contoh: 100" />
                            <x-input-error :messages="$errors->get('stok')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('kasir.items.index') }}" class="text-gray-600 hover:text-gray-900 underline">
                                Batal
                            </a>
                            
                            <x-primary-button id="submitBtn">
                                {{ __('Simpan Menu') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function() {
            var btn = document.getElementById('submitBtn');
            // Ubah teks tombol dan matikan agar tidak diklik dua kali
            btn.innerHTML = 'Sedang Mengupload...';
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        });
    </script>
</x-app-layout>