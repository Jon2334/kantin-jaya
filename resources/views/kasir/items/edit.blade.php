<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Menu') }}: {{ $item->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('kasir.items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Saat Ini</label>
                            @if($item->image)
                                {{-- Cek apakah gambar dari Cloudinary (http) atau Lokal --}}
                                @if(Str::startsWith($item->image, 'http'))
                                    <img src="{{ $item->image }}" alt="Menu Image" class="w-32 h-32 object-cover rounded border">
                                @else
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="Menu Image" class="w-32 h-32 object-cover rounded border">
                                @endif
                            @else
                                <span class="text-gray-500 italic">Tidak ada gambar</span>
                            @endif
                        </div>

                        <div class="mb-4">
                            <x-input-label for="image" :value="__('Ganti Foto (Opsional)')" />
                            <input id="image" class="block mt-1 w-full border border-gray-300 rounded p-2" type="file" name="image" accept="image/*" />
                            <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah foto.</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="nama" :value="__('Nama Menu')" />
                            <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama', $item->nama)" required />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="harga" :value="__('Harga')" />
                            <x-text-input id="harga" class="block mt-1 w-full" type="number" name="harga" :value="old('harga', $item->harga)" required />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="stok" :value="__('Stok')" />
                            <x-text-input id="stok" class="block mt-1 w-full" type="number" name="stok" :value="old('stok', $item->stok)" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('kasir.items.index') }}" class="text-gray-600 hover:text-gray-900 underline mr-4">Batal</a>
                            <x-primary-button>{{ __('Update Data') }}</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>