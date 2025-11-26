<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Kasir') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- BAGIAN 1: KARTU STATISTIK (RINGKASAN) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Laba Bersih -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-gray-500 text-sm font-medium">Total Saldo (Net)</div>
                    <div class="font-bold text-2xl text-gray-800">
                        Rp {{ number_format($pendapatanBersih, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-green-600 mt-1">In: Rp {{ number_format($pemasukanTotal, 0, ',', '.') }}</div>
                    <div class="text-xs text-red-500">Out: Rp {{ number_format($pengeluaranTotal, 0, ',', '.') }}</div>
                </div>

                <!-- Pesanan Pending -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-gray-500 text-sm font-medium">Pesanan Masuk</div>
                    <div class="font-bold text-2xl">{{ $stats['pending'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Menunggu proses</div>
                </div>

                <!-- Sedang Dimasak -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-sm font-medium">Sedang Dimasak</div>
                    <div class="font-bold text-2xl">{{ $stats['processing'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Di dapur</div>
                </div>

                <!-- Siap Diambil -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-medium">Siap Saji</div>
                    <div class="font-bold text-2xl">{{ $stats['done'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Siap diambil</div>
                </div>
            </div>

            <!-- BAGIAN 2: FILTER & LAPORAN -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-bold text-gray-800">
                            {{ $judulLaporan }}
                        </h3>

                        <!-- Form Filter -->
                        <form action="{{ route('kasir.dashboard') }}" method="GET" class="flex items-center gap-2">
                            <label class="text-sm font-semibold text-gray-600">Filter:</label>
                            <select name="periode" class="border-gray-300 rounded-md text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                                <option value="hari_ini" {{ $filter == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                                <option value="bulan_ini" {{ $filter == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="tahun_ini" {{ $filter == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                                <option value="semua" {{ $filter == 'semua' ? 'selected' : '' }}>Semua Riwayat</option>
                            </select>
                            
                            <!-- Tombol Cetak -->
                            <a href="{{ route('kasir.dashboard', ['periode' => $filter, 'print' => 'true']) }}" target="_blank" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-bold flex items-center gap-2 ml-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Cetak Laporan
                            </a>
                        </form>
                    </div>

                    <!-- Total Omzet Filter Ini -->
                    <div class="mb-4 p-4 bg-gray-50 rounded border border-gray-200 flex justify-between items-center">
                        <span class="text-gray-600 font-medium">Total Omzet ({{ $judulLaporan }}):</span>
                        <span class="text-xl font-bold text-green-600">Rp {{ number_format($omzetLaporan, 0, ',', '.') }}</span>
                    </div>

                    <!-- Tabel Data -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 align-middle">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail Menu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($orders as $order)
                                    <tr class="hover:bg-gray-50 transition">
                                        <!-- ID -->
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-indigo-600">#ORD-{{ $order->id }}</td>
                                        
                                        <!-- Waktu -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->created_at->format('d M Y, H:i') }}
                                        </td>

                                        <!-- Pelanggan -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">{{ $order->user->name }}</div>
                                            <div class="text-xs text-gray-500 italic">{{ $order->user->email }}</div>
                                        </td>

                                        <!-- Menu -->
                                        <td class="px-6 py-4">
                                            <ul class="text-sm text-gray-600 list-disc list-inside">
                                                @php $subtotalOrder = 0; @endphp
                                                @foreach($order->orderDetails as $detail)
                                                    <li>{{ $detail->item->nama }} (x{{ $detail->qty }})</li>
                                                    @php $subtotalOrder += $detail->subtotal; @endphp
                                                @endforeach
                                            </ul>
                                        </td>

                                        <!-- Metode Pembayaran (KOLOM BARU) -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($order->payment_method == 'qris')
                                                <span class="px-2 py-1 text-xs font-bold bg-purple-100 text-purple-800 rounded-full uppercase">
                                                    QRIS
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-bold bg-gray-100 text-gray-800 rounded-full uppercase">
                                                    TUNAI
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($order->status == 'pending')
                                                <span class="px-2 py-1 text-xs font-bold rounded bg-yellow-100 text-yellow-800">Pending</span>
                                            @elseif($order->status == 'processing')
                                                <span class="px-2 py-1 text-xs font-bold rounded bg-blue-100 text-blue-800">Dimasak</span>
                                            @elseif($order->status == 'done')
                                                <span class="px-2 py-1 text-xs font-bold rounded bg-green-100 text-green-800">Selesai</span>
                                            @endif
                                        </td>

                                        <!-- Total -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-800">
                                            Rp {{ number_format($subtotalOrder, 0, ',', '.') }}
                                        </td>

                                        <!-- Aksi (Cetak Struk) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($order->status == 'done')
                                                <form action="{{ route('kasir.order.selesai', $order->id) }}" method="POST" target="_blank">
                                                    @csrf
                                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs underline">
                                                        Struk
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-10 text-center text-gray-500 italic">
                                            Tidak ada transaksi pada periode ini.
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