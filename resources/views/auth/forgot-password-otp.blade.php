<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Lupa password? Masukkan email Anda, kami akan mengirimkan kode OTP untuk mereset password.') }}
    </div>

    @if ($errors->any())
        <div class="mb-4 text-red-600 text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email.otp') }}">
        @csrf
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Kirim Kode OTP') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>