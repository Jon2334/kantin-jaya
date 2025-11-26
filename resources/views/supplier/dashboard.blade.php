<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Supplier - Pesanan Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6">
                @forelse($orders as $order)
                    <div class="bg-white p-6 rounded-lg shadow-md border-l-4 {{ $order->status == 'shipped' ? 'border-green-500' : 'border-orange-500' }}">
                        
                        <!-- Header Card -->
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-bold text-lg text-gray-800">Order #PO-{{ $order->id }}</h3>
                                <p class="text-sm text-gray-500">Dari: {{ $order->user->name }} (Kasir)</p>
                                <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-xl font-bold text-indigo-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
                                <span class="px-2 py-1 rounded text-xs font-bold uppercase inline-block mt-1
                                    {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $order->status == 'paid' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $order->status == 'shipped' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>

                        <!-- Detail Barang -->
                        <div class="bg-gray-50 p-3 rounded mb-4 flex justify-between items-center">
                            <span class="font-bold">{{ $order->inventory->nama_barang }}</span>
                            <span class="bg-white border px-2 py-1 rounded font-mono font-bold">{{ $order->jumlah }} {{ $order->inventory->satuan }}</span>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="flex gap-3 mt-4 border-t pt-4">
                            
                            <!-- 1. Tombol Struk (Selalu Ada) -->
                            <a href="{{ route('supplier.struk', $order->id) }}" target="_blank" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded font-bold text-sm">
                                🖨️ Cetak Tagihan
                            </a>

                            <!-- 2. Tombol Konfirmasi Bayar (Jika Pending) -->
                            @if($order->status == 'pending')
                                <form action="{{ route('supplier.confirm', $order->id) }}" method="POST">
                                    @csrf
                                    <button onclick="return confirm('Pastikan Kasir sudah transfer!')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded font-bold text-sm">
                                        💰 Terima Pembayaran
                                    </button>
                                </form>
                            @endif

                            <!-- 3. Form Kirim Barang (Jika Paid) -->
                            @if($order->status == 'paid')
                                <form action="{{ route('supplier.kirim', $order->id) }}" method="POST" class="flex-1 flex gap-2 bg-blue-50 p-2 rounded items-end">
                                    @csrf
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-600">Tgl Kirim</label>
                                        <input type="date" name="tanggal_kirim" required class="w-full text-xs rounded border-gray-300 p-1">
                                    </div>
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-600">No. Resi</label>
                                        <input type="text" name="nomor_resi" required placeholder="RESI-123" class="w-full text-xs rounded border-gray-300 p-1">
                                    </div>
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-600">Catatan</label>
                                        <input type="text" name="catatan" placeholder="Catatan..." class="w-full text-xs rounded border-gray-300 p-1">
                                    </div>
                                    <button type="submit" class="px-4 py-1 bg-green-600 hover:bg-green-700 text-white rounded font-bold text-xs h-8">
                                        🚚 Kirim & Tambah Stok
                                    </button>
                                </form>
                            @endif
                            
                            <!-- Info Jika Sudah Dikirim -->
                            @if($order->status == 'shipped')
                                <div class="flex-1 text-right text-xs text-gray-500">
                                    <p>Dikirim: {{ $order->tanggal_kirim }}</p>
                                    <p>Resi: {{ $order->nomor_resi }}</p>
                                </div>
                            @endif
                        </div>

                    </div>
                @empty
                    <div class="text-center py-10 bg-white rounded">Belum ada pesanan masuk.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>