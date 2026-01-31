<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Pencarian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4">
                <a href="{{ url()->previous() }}" class="text-indigo-600 hover:text-indigo-900 font-bold">
                    &larr; Kembali
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
                            <div class="border rounded-lg p-4 hover:shadow-lg transition duration-200">
                                @if($item->image)
                                    <img src="{{ $item->image }}" alt="{{ $item->nama }}" class="w-full h-40 object-cover rounded-md mb-4">
                                @else
                                    <div class="w-full h-40 bg-gray-200 rounded-md mb-4 flex items-center justify-center text-gray-500">
                                        No Image
                                    </div>
                                @endif

                                <h4 class="font-bold text-lg text-gray-800">{{ $item->nama }}</h4>
                                
                                <div class="flex justify-between items-center mt-4">
                                    <span class="text-indigo-600 font-bold">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                    
                                    @if($role === 'pembeli')
                                        <form action="{{ route('pembeli.order.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                                                Pesan
                                            </button>
                                        </form>
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