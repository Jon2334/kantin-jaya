<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Menu & Stok') }}
            </h2>
            
            <div class="flex gap-2">
                {{-- Tombol Cetak Laporan --}}
                <a href="{{ route('kasir.items.print') }}" target="_blank" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow flex items-center transition duration-150 ease-in-out text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Laporan
                </a>

                {{-- Tombol Tambah Menu --}}
                <a href="{{ route('kasir.items.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow flex items-center transition duration-150 ease-in-out text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Menu Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Notifikasi Sukses --}}
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                            <p class="font-bold">Sukses!</p>
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 align-middle">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-10">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-24">Foto</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Detail Menu</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sisa Stok</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($items as $item)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        {{-- 1. NOMOR URUT --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $loop->iteration }}
                                        </td>

                                        {{-- 2. FOTO (Support Cloudinary & Local) --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($item->image)
                                                @if(str_starts_with($item->image, 'http'))
                                                    <img src="{{ $item->image }}" alt="{{ $item->nama }}" class="h-16 w-16 rounded-md object-cover border shadow-sm">
                                                @else
                                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->nama }}" class="h-16 w-16 rounded-md object-cover border shadow-sm">
                                                @endif
                                            @else
                                                <div class="h-16 w-16 rounded-md bg-gray-200 flex items-center justify-center text-gray-400 text-xs text-center border">
                                                    No IMG
                                                </div>
                                            @endif
                                        </td>

                                        {{-- 3. NAMA & DESKRIPSI (Digabung agar rapi) --}}
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900 text-lg">{{ $item->nama }}</div>
                                            {{-- Menampilkan deskripsi yang dipotong jika terlalu panjang --}}
                                            <div class="text-xs text-gray-500 mt-1 max-w-xs truncate">
                                                {{ \Illuminate\Support\Str::limit($item->deskripsi ?? 'Tidak ada deskripsi', 80) }}
                                            </div>
                                        </td>

                                        {{-- 4. HARGA --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">
                                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                                        </td>

                                        {{-- 5. STATUS STOK --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($item->stok <= 5)
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                                    {{ $item->stok }} (Kritis)
                                                </span>
                                            @else
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                                    {{ $item->stok }} Aman
                                                </span>
                                            @endif
                                        </td>

                                        {{-- 6. AKSI --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex justify-center space-x-3">
                                                <a href="{{ route('kasir.items.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold flex items-center transition">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    Edit
                                                </a>
                                                
                                                <form action="{{ route('kasir.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu {{ $item->nama }}? Data tidak bisa dikembalikan.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold flex items-center transition">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500">
                                                <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                                <p class="text-lg font-medium">Belum ada data menu.</p>
                                                <p class="text-sm">Silakan tambahkan menu baru untuk mulai berjualan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>