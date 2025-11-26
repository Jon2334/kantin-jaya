<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Kantin Jaya') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- FontAwesome CDN -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- Background Gradient yang Keren -->
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500">
            
            <!-- Logo Brand di Atas Form -->
            <div class="mb-6 text-center">
                <a href="/" class="flex flex-col items-center group">
                    <div class="bg-white p-4 rounded-full shadow-2xl group-hover:scale-110 transition duration-300 ease-in-out">
                        <!-- Ikon Utensils (Sendok Garpu) -->
                        <i class="fas fa-utensils text-4xl text-indigo-600"></i>
                    </div>
                    <h1 class="mt-4 text-4xl font-extrabold text-white tracking-tight drop-shadow-lg">
                        KANTIN JAYA
                    </h1>
                    <p class="text-indigo-100 text-sm mt-1 font-medium tracking-wider uppercase">Sistem Informasi Kantin</p>
                </a>
            </div>

            <!-- Card Form (Login/Register) -->
            <div class="w-full sm:max-w-md mt-2 px-8 py-8 bg-white shadow-2xl overflow-hidden sm:rounded-2xl transform transition-all hover:shadow-3xl relative z-10">
                {{ $slot }}
            </div>

            <!-- Footer Simple di Halaman Login -->
            <div class="mt-8 text-white text-xs opacity-80">
                &copy; {{ date('Y') }} Kantin Jaya. All rights reserved.
            </div>
        </div>
    </body>
</html>