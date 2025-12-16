<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Menu') }}: <span class="text-indigo-600">{{ $item->nama }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form id="editForm" action="{{ route('kasir.items.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Saat Ini:</label>
                            @if($item->image)
                                {{-- Logika Cerdas: Cek apakah gambar dari Cloudinary (http) atau Local Storage --}}
                                @if(str_starts_with($item->image, 'http'))
                                    <img src="{{ $item->image }}" alt="{{ $item->nama }}" class="w-32 h-32 object-cover rounded-lg border shadow-sm">
                                    <p class="text-xs text-green-600 mt-1">Sumber: Cloudinary (Online)</p>
                                @else
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->nama }}" class="w-32 h-32 object-cover rounded-lg border shadow-sm">
                                    <p class="text-xs text-orange-600 mt-1">Sumber: Local Storage</p>
                                @endif
                            @else
                                <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-sm">
                                    No Image
                                </div>
                            @endif
                        </div>

                        <div>
                            <x-input-label for="image" :value="__('Ganti Foto (Opsional)')" />
                            <input id="image" 
                                   class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2" 
                                   type="file" 
                                   name="image" 
                                   accept="image/*" />
                            <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah foto.</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="nama" :value="__('Nama Menu')" />
                            <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama', $item->nama)" required />
                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="harga" :value="__('Harga (Rp)')" />
                            <x-text-input id="harga" class="block mt-1 w-full" type="number" name="harga" :value="old('harga', $item->harga)" required min="0" />
                            <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="stok" :value="__('Stok')" />
                            <x-text-input id="stok" class="block mt-1 w-full" type="number" name="stok" :value="old('stok', $item->stok)" required min="0" />
                            <x-input-error :messages="$errors->get('stok')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4 border-t pt-4">
                            <a href="{{ route('kasir.items.index') }}" class="text-gray-600 hover:text-gray-900 underline">
                                Batal
                            </a>
                            <x-primary-button id="submitBtn">
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('editForm').addEventListener('submit', function() {
            var btn = document.getElementById('submitBtn');
            btn.innerHTML = 'Sedang Mengupdate...'; // Ganti teks tombol
            btn.disabled = true; // Matikan tombol
            btn.classList.add('opacity-50', 'cursor-not-allowed'); // Ubah visual jadi pudar
        });
    </script>
</x-app-layout>