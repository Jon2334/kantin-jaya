<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Pencarian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- ALERT SUKSES / GAGAL --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Gagal Memesan!</strong>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- TOMBOL KEMBALI KE DASHBOARD --}}
            <div class="mb-4">
                @php
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
                        <p class="text-gray-500">Tidak ditemukan. Coba kata kunci lain.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($results as $item)
                            <div class="border rounded-lg p-4 hover:shadow-lg transition duration-200 flex flex-col justify-between relative overflow-hidden">
                                
                                {{-- LOGIKA TAMPILAN GAMBAR & STOK HABIS --}}
                                <div class="relative">
                                    @if($item->image)
                                        {{-- Jika stok habis, gambar jadi hitam putih (grayscale) --}}
                                        <img src="{{ $item->image }}" alt="{{ $item->nama }}" 
                                             class="w-full h-40 object-cover rounded-md mb-4 {{ $item->stok <= 0 ? 'grayscale opacity-50' : '' }}">
                                    @else
                                        <div class="w-full h-40 bg-gray-200 rounded-md mb-4 flex items-center justify-center text-gray-500">
                                            No Image
                                        </div>
                                    @endif

                                    {{-- BADGE HABIS (Sesuai Screenshot Dashboard) --}}
                                    @if($item->stok <= 0)
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="bg-black bg-opacity-70 text-white font-bold px-4 py-2 rounded text-lg tracking-widest border-2 border-white transform -rotate-12">
                                                HABIS
                                            </span>
                                        </div>
                                    @else
                                        {{-- Badge Stok Tersedia --}}
                                        <div class="absolute top-2 right-2">
                                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-green-400">
                                                Stok: {{ $item->stok }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <h4 class="font-bold text-lg text-gray-800">{{ $item->nama }}</h4>
                                    <p class="text-gray-600 text-sm mb-2">{{ \Illuminate\Support\Str::limit($item->description ?? '', 50) }}</p>
                                </div>
                                
                                <div class="flex justify-between items-end mt-4 border-t pt-4">
                                    <span class="text-indigo-600 font-bold text-lg">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                    
                                    {{-- FORM PEMESANAN (Hanya Pembeli & Jika Stok Ada) --}}
                                    @if($role === 'pembeli')
                                        @if($item->stok > 0)
                                            <form action="{{ route('pembeli.order.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                
                                                {{-- PERBAIKAN 1: Input Hidden Payment Method (Default: tunai) --}}
                                                {{-- Sesuaikan value="tunai" dengan database kamu (bisa 'cash' atau 'tunai') --}}
                                                <input type="hidden" name="payment_method" value="tunai">

                                                <div class="flex items-center space-x-2">
                                                    {{-- PERBAIKAN 2: Name diganti jadi 'qty' (bukan quantity) --}}
                                                    <input type="number" name="qty" value="1" min="1" max="{{ $item->stok }}" 
                                                           class="w-16 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm p-1 text-center">
                                                    
                                                    <button type="submit" onclick="return confirm('Pesan {{ $item->nama }}?')" 
                                                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded hover:bg-indigo-700 transition">
                                                        Pesan
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            {{-- Tombol Mati Jika Habis --}}
                                            <button disabled class="px-4 py-2 bg-gray-300 text-gray-500 text-sm font-semibold rounded cursor-not-allowed">
                                                Habis
                                            </button>
                                        @endif
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