<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Stok Bahan Baku Dapur') }}
            </h2>
            <div class="flex gap-2">
                <!-- Tombol Cetak Laporan (BARU) -->
                <a href="{{ route('dapur.inventory.print') }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Laporan
                </a>
                
                <!-- Tombol Tambah (Jika masih diperbolehkan) -->
                @if(false) <!-- Ubah ke true jika ingin menampilkan tombol tambah lagi -->
                <a href="{{ route('dapur.inventory.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                    + Tambah Bahan Baru
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Pesan Sukses -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 align-middle">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Bahan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Stok</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($inventories as $index => $item)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">{{ $item->nama_barang }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-lg font-mono font-bold text-blue-600">
                                                {{ (float)$item->stok }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item->satuan }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($item->stok <= 5)
                                                <span class="px-2 py-1 text-xs font-bold text-red-800 bg-red-100 rounded-full">
                                                    KRITIS
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-bold text-green-800 bg-green-100 rounded-full">
                                                    AMAN
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <button onclick="bukaModalPakai({{ $item->id }}, '{{ $item->nama_barang }}', '{{ $item->satuan }}')" 
                                                class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded text-xs font-bold shadow-sm transition duration-150 ease-in-out">
                                                📉 Catat Pakai
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                                            Belum ada data bahan baku.
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

    <!-- MODAL POP-UP (Sama seperti sebelumnya) -->
    <div id="modalPakai" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 transition-opacity">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 mb-4">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Catat Pemakaian</h3>
                
                <div class="mt-2 px-4 py-3">
                    <p class="text-sm text-gray-500 mb-4">
                        Berapa banyak <span id="namaBahan" class="font-bold text-gray-800"></span> yang digunakan?
                    </p>
                    
                    <form id="formPakai" method="POST">
                        @csrf
                        <div class="flex items-center justify-center gap-2">
                            <input type="number" 
                                   step="0.01" 
                                   name="jumlah_pakai" 
                                   class="border border-gray-300 rounded px-3 py-2 w-24 text-center focus:ring-orange-500 focus:border-orange-500" 
                                   placeholder="0" 
                                   required 
                                   min="0.01">
                            
                            <span id="satuanBahan" class="text-gray-600 font-bold text-sm"></span>
                        </div>

                        <div class="items-center px-4 py-3 mt-4 flex flex-col gap-2">
                            <button type="submit" class="w-full px-4 py-2 bg-orange-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                                Simpan Data
                            </button>
                            <button type="button" onclick="tutupModal()" class="w-full px-4 py-2 bg-white text-gray-700 text-base font-medium rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none transition">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bukaModalPakai(id, nama, satuan) {
            document.getElementById('namaBahan').innerText = nama;
            document.getElementById('satuanBahan').innerText = satuan;
            let url = "{{ route('dapur.inventory.kurangi', ':id') }}";
            url = url.replace(':id', id);
            document.getElementById('formPakai').action = url;
            document.getElementById('modalPakai').classList.remove('hidden');
        }

        function tutupModal() {
            document.getElementById('modalPakai').classList.add('hidden');
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                tutupModal();
            }
        });
    </script>
</x-app-layout>