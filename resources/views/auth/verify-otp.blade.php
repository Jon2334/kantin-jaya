<x-guest-layout>
    <div class="px-4 py-2">
        
        <div class="text-center mb-6">
            <div class="bg-indigo-100 p-3 rounded-full inline-flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Verifikasi Akun</h2>
            <p class="text-sm text-gray-500 mt-2">
                Demi keamanan, masukkan 6 digit kode OTP yang telah kami kirim ke email:
            </p>
            <p class="font-medium text-indigo-600 mt-1">
                {{ session('register_email') }}
            </p>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-200 text-green-700 text-sm rounded-lg text-center">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-200 text-red-700 text-sm rounded-lg text-center">
                @foreach ($errors->all() as $error)
                    <p>⚠️ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('otp.store') }}">
            @csrf

            <div class="mb-6">
                <x-input-label for="otp" :value="__('Kode OTP')" class="sr-only" />
                
                <input id="otp" 
                       class="block w-full text-center text-3xl font-bold tracking-[0.5em] text-gray-800 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm p-3 placeholder-gray-300" 
                       type="text" 
                       name="otp" 
                       maxlength="6" 
                       placeholder="000000"
                       required 
                       autofocus 
                       autocomplete="one-time-code"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                       
                <p class="text-xs text-center text-gray-400 mt-2">Masukkan 6 angka kode OTP</p>
            </div>

            <div class="flex items-center justify-center">
                <x-primary-button class="w-full justify-center py-3 text-lg">
                    {{ __('Verifikasi Sekarang') }}
                </x-primary-button>
            </div>
        </form>

        <div class="mt-8 text-center border-t pt-4">
            <p class="text-sm text-gray-600 mb-3">
                Belum menerima kode?
            </p>
            
            <form method="POST" action="{{ route('otp.resend') }}"> 
                @csrf
                <button type="submit" class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm hover:underline transition">
                    Kirim Ulang Kode
                </button>
            </form>

            <div class="mt-4">
                <a href="{{ route('login') }}" class="text-xs text-gray-400 hover:text-gray-600 underline">
                    Salah alamat email? Masuk / Daftar ulang
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>