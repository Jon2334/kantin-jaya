<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Pencarian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- ALERT NOTIFIKASI --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Gagal Memesan!</strong>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-4">
                @php $dashboardRoute = Auth::user()->role . '.dashboard'; @endphp
                <a href="{{ route($dashboardRoute) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 font-bold transition">
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
                                
                                {{-- GAMBAR PRODUK --}}
                                <div class="relative">
                                    @if($item->image)
                                        <img src="{{ $item->image }}" alt="{{ $item->nama }}" 
                                             class="w-full h-40 object-cover rounded-md mb-4 {{ $item->stok <= 0 ? 'grayscale opacity-50' : '' }}">
                                    @else
                                        <div class="w-full h-40 bg-gray-200 rounded-md mb-4 flex items-center justify-center text-gray-500">No Image</div>
                                    @endif

                                    @if($item->stok <= 0)
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="bg-black bg-opacity-70 text-white font-bold px-4 py-2 rounded text-lg transform -rotate-12">HABIS</span>
                                        </div>
                                    @else
                                        <div class="absolute top-2 right-2">
                                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-green-400">
                                                Stok: {{ $item->stok }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- INFO PRODUK --}}
                                <div>
                                    <h4 class="font-bold text-lg text-gray-800">{{ $item->nama }}</h4>
                                    <p class="...">{{ \Illuminate\Support\Str::limit($item->deskripsi ?? '', 50) }}</p>
                                    <p class="text-indigo-600 font-bold text-lg">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                                </div>
                                
                                {{-- FORM PEMESANAN --}}
                                <div class="mt-4 border-t pt-4">
                                    @if($role === 'pembeli')
                                        @if($item->stok > 0)
                                            <form action="{{ route('pembeli.order.store') }}" method="POST" id="form-{{ $item->id }}" onsubmit="return processOrder(event, {{ $item->id }})">
                                                @csrf
                                                
                                                {{-- PILIHAN METODE PEMBAYARAN --}}
                                                <div class="mb-2">
                                                    <label class="text-xs text-gray-600 font-semibold">Metode Bayar:</label>
                                                    <select name="payment_method" id="payment-{{ $item->id }}" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                        <option value="tunai">Tunai (Cash)</option>
                                                        <option value="qris">QRIS (Scan)</option>
                                                    </select>
                                                </div>

                                                <div class="flex items-center justify-between space-x-2">
                                                    {{-- INPUT QTY ARRAY (FIX BUG: name="qty[{{ $item->id }}]") --}}
                                                    <div class="flex items-center">
                                                        <span class="text-sm mr-2 text-gray-500">Jml:</span>
                                                        <input type="number" name="qty[{{ $item->id }}]" value="1" min="1" max="{{ $item->stok }}" 
                                                               class="w-16 rounded-md border-gray-300 text-center text-sm">
                                                    </div>
                                                    
                                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded hover:bg-indigo-700 transition">
                                                        Pesan
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <button disabled class="w-full px-4 py-2 bg-gray-300 text-gray-500 text-sm font-semibold rounded cursor-not-allowed">
                                                Stok Habis
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

    {{-- MODAL QRIS (Hidden by Default) --}}
    <div id="qrisModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="bg-white rounded-lg shadow-2xl p-6 w-full max-w-sm text-center transform transition-all scale-100">
            <h2 class="text-2xl font-bold mb-2 text-gray-800">Scan QRIS</h2>
            <p class="text-gray-500 text-sm mb-4">Silakan scan kode di bawah untuk membayar</p>
            
            {{-- Ganti URL ini dengan gambar QRIS asli kamu --}}
            <div class="bg-gray-100 p-4 rounded-lg mb-4 inline-block border-2 border-dashed border-gray-300">
                <img src="https://upload.wikimedia.org/wikipedia/commons/d/d0/QR_code_for_mobile_English_Wikipedia.svg" 
                     alt="QRIS Code" class="w-48 h-48 mx-auto">
            </div>

            <p class="text-xs text-gray-400 mb-6">Kantin Jaya Payment System</p>

            <div class="flex space-x-3 justify-center">
                <button onclick="closeQrisModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                    Batal
                </button>
                <button onclick="confirmQrisPayment()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold shadow-md transition">
                    Sudah Bayar
                </button>
            </div>
        </div>
    </div>

    {{-- JAVASCRIPT LOGIC --}}
    <script>
        let pendingFormId = null;

        function processOrder(event, itemId) {
            event.preventDefault(); // Stop submit sementara
            
            const paymentSelect = document.getElementById('payment-' + itemId);
            const method = paymentSelect.value;
            const form = document.getElementById('form-' + itemId);

            if (method === 'qris') {
                // Jika QRIS, Tampilkan Modal
                pendingFormId = itemId;
                document.getElementById('qrisModal').classList.remove('hidden');
            } else {
                // Jika Tunai, Langsung Submit
                if(confirm('Pesan dengan pembayaran Tunai?')) {
                    form.submit();
                }
            }
            return false;
        }

        function closeQrisModal() {
            document.getElementById('qrisModal').classList.add('hidden');
            pendingFormId = null;
        }

        function confirmQrisPayment() {
            if (pendingFormId) {
                const form = document.getElementById('form-' + pendingFormId);
                form.submit(); // Submit form yang tertunda
            }
        }
    </script>
</x-app-layout>