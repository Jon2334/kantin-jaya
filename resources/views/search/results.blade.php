<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Pencarian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- BAGIAN 1: MENAMPILKAN PESAN SUKSES/ERROR (Supaya tombol Pesan terasa berfungsi) --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Gagal!</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- BAGIAN 2: TOMBOL KEMBALI YANG CERDAS (Sesuai Role) --}}
            <div class="mb-4">
                @php
                    // Logika otomatis kembali ke dashboard masing-masing role
                    $dashboardRoute = Auth::user()->role . '.dashboard';
                @endphp
                
                <a href="{{ route($dashboardRoute) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 font-bold transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Menampilkan hasil untuk: "<span class="font-bold text-indigo-600">{{ $query }}</span>"
                </h3>

                @if($results->isEmpty())
                    <div class="text-center py-10">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ditemukan</h3>
                        <p class="mt-1 text-sm text-gray-500">Coba kata kunci lain atau periksa ejaan Anda.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($results as $item)
                            <div class="border rounded-lg p-4 hover:shadow-lg transition duration-200 flex flex-col justify-between">
                                <div>
                                    @if($item->image)
                                        <img src="{{ $item->image }}" alt="{{ $item->nama }}" class="w-full h-40 object-cover rounded-md mb-4">
                                    @else
                                        <div class="w-full h-40 bg-gray-200 rounded-md mb-4 flex items-center justify-center text-gray-500">
                                            No Image
                                        </div>
                                    @endif

                                    <h4 class="font-bold text-lg text-gray-800">{{ $item->nama }}</h4>
                                    {{-- Menggunakan Str::limit agar deskripsi tidak kepanjangan --}}
                                    <p class="text-gray-600 text-sm mb-2">{{ \Illuminate\Support\Str::limit($item->description ?? '', 50) }}</p>
                                </div>
                                
                                <div class="flex justify-between items-end mt-4 border-t pt-4">
                                    <span class="text-indigo-600 font-bold text-lg">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                    
                                    {{-- BAGIAN 3: TOMBOL PESAN (Hanya untuk Pembeli) --}}
                                    @if($role === 'pembeli')
                                        <form action="{{ route('pembeli.order.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                                            
                                            {{-- Tips: Tambahkan validasi klik dengan javascript sederhana --}}
                                            <div class="flex items-center space-x-2">
                                                {{-- Input Quantity Kecil --}}
                                                <input type="number" name="quantity" value="1" min="1" class="w-16 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm p-1 text-center">
                                                
                                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin memesan {{ $item->nama }}?')" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded hover:bg-indigo-700 transition">
                                                    Pesan
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>