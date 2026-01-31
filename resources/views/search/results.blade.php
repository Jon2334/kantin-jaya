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
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ditemukan</h3>
                        <p class="mt-1 text-sm text-gray-500">Coba kata kunci lain atau periksa ejaan Anda.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($results as $item)
                            <div class="border rounded-lg p-4 hover:shadow-lg transition duration-200">
                                @if($item->image)
                                    <img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-full h-40 object-cover rounded-md mb-4">
                                @else
                                    <div class="w-full h-40 bg-gray-200 rounded-md mb-4 flex items-center justify-center text-gray-500">
                                        No Image
                                    </div>
                                @endif

                                <h4 class="font-bold text-lg text-gray-800">{{ $item->name }}</h4>
                                <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ $item->description }}</p>
                                <div class="flex justify-between items-center mt-4">
                                    <span class="text-indigo-600 font-bold">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                    
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