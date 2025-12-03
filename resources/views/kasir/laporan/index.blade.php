<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- FILTER & SUMMARY -->
            <div class="bg-white p-6 rounded-lg shadow flex flex-col md:flex-row justify-between items-center gap-4">
                
                <!-- Form Filter -->
                <form action="{{ route('kasir.laporan') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
                    <label class="font-bold text-gray-700">Periode:</label>
                    <select name="periode" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                        <option value="hari_ini" {{ $filter == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="bulan_ini" {{ $filter == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="tahun_ini" {{ $filter == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="semua" {{ $filter == 'semua' ? 'selected' : '' }}>Semua Data</option>
                    </select>
                </form>

                <!-- Total Omzet -->
                <div class="text-right">
                    <p class="text-sm text-gray-500">Total Omzet {{ $judulLaporan }}</p>
                    <h3 class="text-3xl font-bold text-green-600">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h3>
                </div>

                <!-- Tombol Cetak -->
                <a href="{{ route('kasir.laporan', ['periode' => $filter, 'print' => 'true']) }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-bold shadow flex items-center">
                    🖨️ Cetak PDF
                </a>
            </div>

            <!-- TABEL DATA -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold mb-4 text-gray-800">{{ $judulLaporan }}</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Order</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail Menu</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($laporan as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-bold text-gray-700">#ORD-{{ $order->id }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $order->user->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        <ul class="list-disc list-inside">
                                            @php $subtotal = 0; @endphp
                                            @foreach($order->orderDetails as $detail)
                                                <li>{{ $detail->item->nama }} (x{{ $detail->qty }})</li>
                                                @php $subtotal += $detail->subtotal; @endphp
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="px-4 py-3 text-sm uppercase font-bold text-gray-500">{{ $order->payment_method }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-bold text-green-600">
                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">
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
</x-app-layout>