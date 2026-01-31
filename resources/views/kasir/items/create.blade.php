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
                                   class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
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
                            <x-input-label for="deskripsi" :value="__('Deskripsi Menu (Opsional)')" />
                            <textarea id="deskripsi" 
                                      name="deskripsi" 
                                      rows="3" 
                                      class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                      placeholder="Jelaskan detail menu ini. Contoh: Nasi goreng dengan toping telur mata sapi dan kerupuk udang.">{{ old('deskripsi') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Deskripsi ini akan muncul di hasil pencarian pembeli.</p>
                            <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="harga" :value="__('Harga (Rupiah)')" />
                                <div class="relative mt-1 rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="harga" id="harga" 
                                           class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                           placeholder="15000" 
                                           value="{{ old('harga') }}" 
                                           required min="0">
                                </div>
                                <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="stok" :value="__('Stok Awal')" />
                                <x-text-input id="stok" class="block mt-1 w-full" type="number" name="stok" :value="old('stok')" required min="0" placeholder="100" />
                                <x-input-error :messages="$errors->get('stok')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 border-t pt-4">
                            <a href="{{ route('kasir.items.index') }}" class="text-gray-600 hover:text-gray-900 underline font-medium">
                                Batal
                            </a>
                            
                            <x-primary-button id="submitBtn" class="bg-indigo-600 hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900">
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
            // Ubah teks tombol dan matikan agar tidak diklik dua kali saat upload
            btn.innerHTML = 'Sedang Mengupload...';
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        });
    </script>
</x-app-layout>