<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold">Verifikasi Reset Password</h2>
        <p class="text-sm text-gray-500">Kode OTP dikirim ke: {{ session('reset_email') }}</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 text-red-600 text-sm text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.otp.check') }}">
        @csrf
        <div class="mb-6">
            <input id="otp" 
                   class="block w-full text-center text-3xl font-bold tracking-[0.5em] text-gray-800 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm p-3" 
                   type="text" name="otp" maxlength="6" required autofocus 
                   placeholder="000000" oninput="this.value = this.value.replace(/[^0-9]/g, '')"/>
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Verifikasi Kode') }}
        </x-primary-button>
    </form>
</x-guest-layout>