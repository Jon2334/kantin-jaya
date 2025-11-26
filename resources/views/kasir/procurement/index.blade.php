<x-app-layout>
    <!-- ... Header tetap sama ... -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengadaan Bahan Baku (Ke Supplier)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Info Saldo (Tetap sama) -->
            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-indigo-700">
                            Saldo Kas saat ini: 
                            <span class="font-bold text-lg">Rp {{ number_format($saldo ?? 0, 0, ',', '.') }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- FORM REQUEST BARANG (Tetap sama, tidak diubah) -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold mb-4 text-gray-800">Buat Permintaan Baru</h3>
                <form action="{{ route('kasir.procurement.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div class="md:col-span-2">
                        <x-input-label>Pilih Barang (Dari Katalog Supplier)</x-input-label>
                        <select id="inventory_id" name="inventory_id" class="w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required onchange="hitungTotal()">
                            <option value="" data-harga="0" data-stok="0" disabled selected>-- Pilih Barang --</option>
                            @foreach($inventories as $inv)
                                <option value="{{ $inv->id }}" 
                                        data-harga="{{ $inv->harga }}" 
                                        data-stok="{{ (float)$inv->stok_supplier }}">
                                    {{ $inv->nama_barang }} (Stok Supplier: {{ (float)$inv->stok_supplier }} {{ $inv->satuan }} | Rp {{ number_format($inv->harga, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @if($inventories->isEmpty())
                            <p class="text-xs text-red-500 mt-1">Katalog Supplier Kosong.</p>
                        @endif
                    </div>
                    <div>
                        <x-input-label>Jumlah Order</x-input-label>
                        <x-text-input id="jumlah" type="number" step="0.1" name="jumlah" class="w-full" required placeholder="0" oninput="hitungTotal()" />
                        <p id="error_stok" class="text-xs text-red-600 mt-1 font-bold hidden">Stok Supplier Tidak Cukup!</p>
                    </div>
                    <div>
                        <x-input-label>Total Harus Dibayar (Rp)</x-input-label>
                        <x-text-input id="total_harga" type="number" name="total_harga" class="w-full bg-gray-100 cursor-not-allowed font-bold text-indigo-700" readonly tabindex="-1" />
                        <p class="text-xs text-gray-500 mt-1" id="info_harga_satuan">Harga satuan: -</p>
                    </div>
                    <div class="md:col-span-4 text-right">
                        <button type="submit" id="btnSubmit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-bold shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed">
                            🚀 Kirim Request & Bayar
                        </button>
                    </div>
                </form>
            </div>

            <!-- RIWAYAT TRANSAKSI (DIPERBARUI: ADA TOMBOL CETAK) -->
            <div class="bg-white p-6 rounded-lg shadow">
                
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Riwayat Pengeluaran</h3>
                    
                    <!-- TOMBOL CETAK BARU -->
                    <a href="{{ route('kasir.procurement.print') }}" target="_blank" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-bold flex items-center gap-2 shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Laporan
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Biaya</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Resi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($procurements as $p)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $p->created_at->format('d M Y') }}</td>
                                    <td class="px-4 py-3 font-bold text-gray-800">{{ $p->inventory->nama_barang }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $p->jumlah }} {{ $p->inventory->satuan }}</td>
                                    <td class="px-4 py-3 text-sm font-mono text-red-600">- Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-bold
                                            {{ $p->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $p->status == 'paid' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $p->status == 'shipped' ? 'bg-green-100 text-green-800' : '' }}">
                                            {{ strtoupper($p->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $p->nomor_resi ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada riwayat pengadaan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Script JS sama seperti sebelumnya -->
    <script>
        function hitungTotal() {
            const selectBarang = document.getElementById('inventory_id');
            const inputJumlah = document.getElementById('jumlah');
            const inputTotal = document.getElementById('total_harga');
            const infoSatuan = document.getElementById('info_harga_satuan');
            const errorStok = document.getElementById('error_stok');
            const btnSubmit = document.getElementById('btnSubmit');

            const selectedOption = selectBarang.options[selectBarang.selectedIndex];
            const hargaSatuan = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
            const stokSupplier = parseFloat(selectedOption.getAttribute('data-stok')) || 0;
            const jumlah = parseFloat(inputJumlah.value) || 0;

            const total = hargaSatuan * jumlah;
            inputTotal.value = total;
            
            const formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
            infoSatuan.innerText = "Harga satuan: " + formatter.format(hargaSatuan);

            if (jumlah > stokSupplier) {
                errorStok.innerText = "Stok Supplier Tidak Cukup! (Sisa: " + stokSupplier + ")";
                errorStok.classList.remove('hidden');
                btnSubmit.disabled = true;
                btnSubmit.classList.add('bg-gray-400', 'cursor-not-allowed');
                btnSubmit.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
            } else if (stokSupplier === 0) {
                errorStok.innerText = "Stok Habis!";
                errorStok.classList.remove('hidden');
                btnSubmit.disabled = true;
                btnSubmit.classList.add('bg-gray-400', 'cursor-not-allowed');
            } else {
                errorStok.classList.add('hidden');
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('bg-gray-400', 'cursor-not-allowed');
                btnSubmit.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
            }
        }
    </script>
</x-app-layout>