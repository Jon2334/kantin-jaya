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

                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex items-start gap-4">
                            <div class="flex-shrink-0">
                                @if($item->image)
                                    @if(str_starts_with($item->image, 'http'))
                                        <img src="{{ $item->image }}" alt="{{ $item->nama }}" class="w-32 h-32 object-cover rounded-lg border shadow-sm">
                                    @else
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->nama }}" class="w-32 h-32 object-cover rounded-lg border shadow-sm">
                                    @endif
                                @else
                                    <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-sm">
                                        No Image
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Foto Saat Ini</h3>
                                <p class="text-sm text-gray-500 mt-1">Jika tidak ingin mengubah foto, biarkan input upload kosong.</p>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="image" :value="__('Ganti Foto (Opsional)')" />
                            <input id="image" 
                                   class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2 focus:ring-2 focus:ring-indigo-500" 
                                   type="file" 
                                   name="image" 
                                   accept="image/*" />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="nama" :value="__('Nama Menu')" />
                            <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama', $item->nama)" required />
                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="deskripsi" :value="__('Deskripsi Menu')" />
                            {{-- Perhatikan: old('deskripsi', $item->deskripsi) berfungsi menampilkan data lama --}}
                            <textarea id="deskripsi" 
                                      name="deskripsi" 
                                      rows="3" 
                                      class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                      placeholder="Deskripsi menu...">{{ old('deskripsi', $item->deskripsi) }}</textarea>
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
                                           value="{{ old('harga', $item->harga) }}" 
                                           required min="0">
                                </div>
                                <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="stok" :value="__('Stok Saat Ini')" />
                                <x-text-input id="stok" class="block mt-1 w-full" type="number" name="stok" :value="old('stok', $item->stok)" required min="0" />
                                <x-input-error :messages="$errors->get('stok')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 border-t pt-4">
                            <a href="{{ route('kasir.items.index') }}" class="text-gray-600 hover:text-gray-900 underline font-medium">
                                Batal
                            </a>
                            <x-primary-button id="submitBtn" class="bg-indigo-600 hover:bg-indigo-700">
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
            btn.innerHTML = 'Sedang Mengupdate...'; 
            btn.disabled = true; 
            btn.classList.add('opacity-50', 'cursor-not-allowed'); 
        });
    </script>
</x-app-layout>