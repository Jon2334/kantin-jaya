<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pesan Makanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-indigo-700">📋 Pilih Menu</h3>

                    <form id="orderForm" action="{{ route('pembeli.order.store') }}" method="POST" onsubmit="return cekMetodeBayar(event)">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                            @forelse($items as $item)
                                <div class="border rounded-lg shadow-sm hover:shadow-md transition overflow-hidden flex flex-col {{ $item->stok == 0 ? 'bg-gray-100 opacity-75' : 'bg-white' }}">
                                    
                                    {{-- GAMBAR MENU --}}
                                    <div class="h-48 w-full bg-gray-200 relative">
                                        @if($item->image)
                                            <img src="{{ str_starts_with($item->image, 'http') ? $item->image : asset('storage/' . $item->image) }}" 
                                                 alt="{{ $item->nama }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="flex items-center justify-center h-full text-gray-400">
                                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif

                                        @if($item->stok == 0)
                                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                                <span class="text-white font-bold text-lg border-2 border-white px-4 py-1 transform -rotate-12">HABIS</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- DETAIL MENU --}}
                                    <div class="p-4 flex-1 flex flex-col">
                                        <div class="flex justify-between items-start mb-1">
                                            <h4 class="font-bold text-lg leading-tight text-gray-800">{{ $item->nama }}</h4>
                                            @if($item->stok > 0)
                                                <span class="px-2 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full shrink-0">
                                                    Stok: {{ $item->stok }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- [FITUR BARU] DESKRIPSI BACA SELENGKAPNYA --}}
                                        <div class="mb-3" x-data="{ expanded: false }">
                                            {{-- Default line-clamp-2 (potong 2 baris), jika expanded=true maka tampil semua --}}
                                            <p class="text-sm text-gray-500 leading-relaxed transition-all duration-200"
                                               :class="expanded ? '' : 'line-clamp-2'"
                                               title="{{ $item->deskripsi }}">
                                                {{ $item->deskripsi ?? 'Tidak ada deskripsi untuk menu ini.' }}
                                            </p>

                                            {{-- Tombol hanya muncul jika deskripsi lebih dari 60 karakter --}}
                                            @if(strlen($item->deskripsi) > 60)
                                                <button type="button" 
                                                        @click="expanded = ! expanded" 
                                                        class="text-xs text-indigo-600 font-bold mt-1 hover:underline focus:outline-none flex items-center gap-1">
                                                    <span x-show="!expanded">Baca Selengkapnya &darr;</span>
                                                    <span x-show="expanded">Tutup &uarr;</span>
                                                </button>
                                            @endif
                                        </div>
                                        {{-- ========================================== --}}

                                        <p class="text-indigo-600 font-bold mb-4 text-lg">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>

                                        <div class="mt-auto flex items-center justify-between border-t pt-3">
                                            <label class="text-sm text-gray-600 font-medium">Jumlah Pesan:</label>
                                            @if($item->stok > 0)
                                                <input type="number" 
                                                       name="qty[{{ $item->id }}]" 
                                                       value="0" 
                                                       min="0" 
                                                       max="{{ $item->stok }}" 
                                                       class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-center py-1"
                                                >
                                            @else
                                                <input type="text" value="0" disabled class="w-20 bg-gray-200 rounded-md border-gray-300 cursor-not-allowed text-center py-1 text-gray-400">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center text-gray-500 py-10">
                                    <p class="text-xl font-semibold">Menu belum tersedia.</p>
                                    <p class="text-sm">Mohon hubungi kasir untuk update stok.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="border-t pt-6 bg-gray-50 -mx-6 -mb-6 p-6 rounded-b-lg flex flex-col md:flex-row justify-between items-center shadow-inner">
                            
                            <div class="mb-4 md:mb-0 w-full md:w-1/2">
                                <x-input-label for="payment_method" :value="__('Metode Pembayaran')" class="mb-2 font-bold text-gray-700" />
                                <div class="flex space-x-4">
                                    <label class="flex items-center p-3 border rounded-md bg-white cursor-pointer hover:bg-gray-50 w-full ring-inset focus-within:ring-2 ring-indigo-600 transition shadow-sm">
                                        <input type="radio" name="payment_method" value="qris" class="text-indigo-600 focus:ring-indigo-500" required>
                                        <span class="ml-2 font-bold text-gray-700">QRIS (Scan)</span>
                                    </label>
                                    <label class="flex items-center p-3 border rounded-md bg-white cursor-pointer hover:bg-gray-50 w-full ring-inset focus-within:ring-2 ring-indigo-600 transition shadow-sm">
                                        <input type="radio" name="payment_method" value="tunai" class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 font-bold text-gray-700">Tunai (Kasir)</span>
                                    </label>
                                </div>
                                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                            </div>

                            <div class="w-full md:w-auto">
                                <x-primary-button class="w-full md:w-auto px-8 py-3 text-lg flex justify-center bg-indigo-600 hover:bg-indigo-700">
                                    {{ __('🛒 Kirim Pesanan') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-gray-700">🕒 Riwayat Pesanan Anda</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Detail Menu</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($riwayatPesanan as $order)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->created_at->format('H:i, d M') }}
                                            <div class="text-xs text-gray-400 font-mono mt-1">#ORD-{{ $order->id }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <ul class="list-disc list-inside">
                                                @php $total = 0; @endphp
                                                @foreach($order->orderDetails as $detail)
                                                    <li><span class="font-semibold">{{ $detail->item->nama }}</span> ({{ $detail->qty }}x)</li>
                                                    @php $total += $detail->subtotal; @endphp
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                            Rp {{ number_format($total, 0, ',', '.') }}
                                            <div class="text-xs font-normal text-gray-500 uppercase mt-1">{{ $order->payment_method }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($order->status == 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">Menunggu</span>
                                            @elseif($order->status == 'processing')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200">Dimasak</span>
                                            @elseif($order->status == 'done')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-8 text-gray-500">Belum ada pesanan. Ayo pesan sekarang!</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="qrisModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full hidden z-50 backdrop-blur-sm transition-all">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-2xl rounded-xl bg-white text-center transform transition-all scale-100">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                </div>
                <h3 class="text-xl leading-6 font-bold text-gray-900">Pembayaran QRIS</h3>
                <div class="mt-2 px-2 py-3">
                    <p class="text-sm text-gray-500 mb-4">
                        Scan QR Code di bawah menggunakan OVO/GoPay/Dana/ShopeePay.
                    </p>
                    <div class="bg-white p-2 inline-block border-2 border-dashed border-gray-300 rounded-lg">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=KantinJayaPayment" 
                             alt="QRIS Code" 
                             class="mx-auto">
                    </div>
                    
                    <p class="text-xs text-gray-400 mt-4">Jangan tutup halaman ini sebelum pembayaran berhasil.</p>
                </div>
                <div class="px-4 py-3 space-y-3">
                    <button id="btnConfirmQris" onclick="submitForm()" class="w-full px-4 py-3 bg-green-600 text-white text-base font-bold rounded-lg shadow hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-300">
                        ✅ Saya Sudah Bayar
                    </button>
                    <button onclick="tutupModal()" class="w-full px-4 py-3 bg-white text-gray-700 text-base font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition focus:outline-none">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cekMetodeBayar(event) {
            // 1. Ambil metode pembayaran yang dipilih
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            
            // 2. Cek apakah user sudah pilih menu (hitung total qty)
            const inputs = document.querySelectorAll('input[name^="qty"]');
            let totalQty = 0;
            inputs.forEach(input => totalQty += parseInt(input.value || 0));

            if (totalQty === 0) {
                alert("Silakan pilih minimal satu menu makanan!");
                event.preventDefault();
                return false;
            }

            if (!paymentMethod) {
                alert("Silakan pilih metode pembayaran!");
                event.preventDefault();
                return false;
            }

            // 3. Jika metode QRIS, TAHAN form dan munculkan modal
            if (paymentMethod.value === 'qris') {
                event.preventDefault(); // Stop form submit
                document.getElementById('qrisModal').classList.remove('hidden'); // Munculkan Modal
                return false;
            }

            // 4. Jika Tunai, konfirmasi dulu
            return confirm("Apakah pesanan Anda sudah benar? Klik OK untuk memesan.");
        }

        function tutupModal() {
            document.getElementById('qrisModal').classList.add('hidden');
        }

        function submitForm() {
            // Submit form secara manual setelah user klik "Sudah Bayar"
            document.getElementById('orderForm').submit();
        }
    </script>
</x-app-layout>