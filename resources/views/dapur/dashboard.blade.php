<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Monitor Dapur (KDS)') }}
            </h2>
            <div class="text-sm text-gray-500" id="timer">
                Auto-refresh dalam <span id="countdown" class="font-bold text-indigo-600">30</span> detik
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($orders as $order)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 {{ $order->status == 'pending' ? 'border-yellow-500' : 'border-blue-500' }} flex flex-col h-full">
                        <div class="p-6 text-gray-900 flex-1 flex flex-col">
                            <div class="flex justify-between items-start mb-4 border-b pb-2">
                                <div>
                                    <h3 class="font-bold text-lg">#ORD-{{ $order->id }}</h3>
                                    <p class="text-sm text-gray-600">{{ $order->user->name }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-1 text-xs font-bold text-white rounded {{ $order->status == 'pending' ? 'bg-yellow-500' : 'bg-blue-500' }}">
                                        {{ strtoupper($order->status) }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">{{ $order->created_at->format('H:i') }}</p>
                                </div>
                            </div>
                            <ul class="mb-6 space-y-2 flex-1">
                                @foreach($order->orderDetails as $detail)
                                    <li class="flex justify-between items-center text-sm border-b border-dashed pb-1">
                                        <span class="font-bold text-lg text-gray-800 w-8">{{ $detail->qty }}x</span>
                                        <span class="flex-1">{{ $detail->item->nama }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-auto pt-4">
                                @if($order->status == 'pending')
                                    <form action="{{ route('dapur.order.update', $order->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="processing">
                                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out flex items-center justify-center gap-2">
                                            <span>🔥</span> Mulai Masak
                                        </button>
                                    </form>
                                @else
                                    <button onclick="bukaModalSelesai({{ $order->id }}, '#ORD-{{ $order->id }}')" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out flex items-center justify-center gap-2">
                                        <span>✅</span> Selesai Masak
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-10 bg-white rounded-lg shadow">
                        <p class="text-gray-500 text-xl">Tidak ada pesanan aktif.</p>
                        <p class="text-sm text-gray-400">Halaman akan refresh otomatis untuk mengecek pesanan baru.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="modalBahan" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 transition-opacity">
        <div class="relative top-10 mx-auto p-0 border w-full max-w-lg shadow-lg rounded-lg bg-white">
            <div class="bg-gray-100 px-5 py-4 border-b rounded-t-lg flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">
                    Catat Pemakaian Bahan <span id="modalOrderTitle" class="text-indigo-600"></span>
                </h3>
                <button onclick="tutupModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div class="p-5 max-h-[60vh] overflow-y-auto">
                <p class="text-sm text-gray-500 mb-4">Masukkan jumlah bahan yang dipakai.</p>
                <form id="formSelesai" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="done">
                    <div class="space-y-4">
                        @forelse($inventories as $inv)
                            <div class="flex items-center justify-between border p-2 rounded hover:bg-gray-50">
                                <div class="w-1/3">
                                    <label class="font-semibold text-gray-700 block text-sm">{{ $inv->nama_barang }}</label>
                                    <span class="text-xs text-gray-500 bg-gray-200 px-1 rounded">Sisa: {{ (float)$inv->stok }} {{ $inv->satuan }}</span>
                                </div>
                                <div class="flex items-center gap-2 w-2/3">
                                    <input type="number" step="0.001" name="bahan[{{ $inv->id }}][jumlah]" class="w-20 border-gray-300 rounded text-center text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
                                    
                                    <select name="bahan[{{ $inv->id }}][satuan]" class="text-sm border-gray-300 rounded w-24 bg-gray-50">
                                        <option value="{{ $inv->satuan }}">{{ $inv->satuan }}</option>
                                        
                                        @php 
                                            $satuan = strtolower(trim($inv->satuan)); 
                                        @endphp

                                        @if($satuan == 'kg' || $satuan == 'kilogram')
                                            <option value="Gram">Gram</option>
                                        @elseif($satuan == 'liter' || $satuan == 'l')
                                            <option value="ml">ml</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-red-500 italic">Stok bahan baku kosong.</p>
                        @endforelse
                    </div>
                    <div class="mt-6 flex gap-3">
                        <button type="button" onclick="tutupModal()" class="w-1/2 px-4 py-2 bg-gray-200 text-gray-800 font-bold rounded hover:bg-gray-300">Batal</button>
                        <button type="submit" class="w-1/2 px-4 py-2 bg-green-600 text-white font-bold rounded hover:bg-green-700 shadow">Simpan & Selesai</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Set waktu awal
        let timeLeft = 30;
        const countdownEl = document.getElementById('countdown');

        // Simpan interval ID agar bisa dihentikan jika perlu
        const timerInterval = setInterval(() => {
            // Cek apakah modal sedang TERSEMBUNYI (hidden)
            // Kita hanya refresh jika user TIDAK sedang membuka modal input bahan
            if (document.getElementById('modalBahan').classList.contains('hidden')) {
                
                // Kurangi waktu dulu
                timeLeft--;

                // Update tampilan angka
                if (timeLeft >= 0) {
                    countdownEl.innerText = timeLeft;
                }

                // Jika waktu habis
                if (timeLeft <= 0) {
                    // Hentikan interval agar tidak looping reload
                    clearInterval(timerInterval);
                    
                    // Beri feedback visual
                    countdownEl.innerText = "Refreshing...";
                    
                    // Reload halaman
                    window.location.reload();
                }
            }
        }, 1000); // Jalan setiap 1000ms (1 detik)

        function bukaModalSelesai(orderId, orderCode) {
            document.getElementById('modalOrderTitle').innerText = orderCode;
            let url = "{{ route('dapur.order.update', ':id') }}";
            url = url.replace(':id', orderId);
            document.getElementById('formSelesai').action = url;
            
            // Reset input form
            document.querySelectorAll('input[type="number"]').forEach(input => input.value = '');
            
            // Tampilkan modal
            document.getElementById('modalBahan').classList.remove('hidden');
        }

        function tutupModal() {
            document.getElementById('modalBahan').classList.add('hidden');
            // Reset timer ke 30 detik saat modal ditutup agar user punya waktu melihat pesanan lagi
            timeLeft = 30; 
            countdownEl.innerText = timeLeft;
        }
    </script>
</x-app-layout>