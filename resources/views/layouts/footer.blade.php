<footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Bagian 1: Brand & Deskripsi -->
            <div class="flex flex-col space-y-4">
                <div class="flex items-center gap-2">
                    <!-- Logo (Gunakan SVG atau Image jika ada) -->
                    <div class="bg-indigo-600 text-white p-2 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <span class="text-2xl font-extrabold text-gray-800 tracking-tight">KANTIN JAYA</span>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Sistem manajemen kantin modern yang terintegrasi. Memudahkan pemesanan, pengelolaan stok, dan pelaporan keuangan secara real-time.
                </p>
            </div>

            <!-- Bagian 2: Quick Links -->
            <div class="flex flex-col space-y-4 md:pl-10">
                <h3 class="text-lg font-bold text-gray-800">Akses Cepat</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><a href="{{ route('login') }}" class="hover:text-indigo-600 transition">Masuk Akun</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-indigo-600 transition">Daftar Baru</a></li>
                    <!-- Tampilkan link dashboard jika login -->
                    @auth
                        @if(Auth::user()->role == 'kasir')
                            <li><a href="{{ route('kasir.dashboard') }}" class="hover:text-indigo-600 transition">Dashboard Kasir</a></li>
                        @elseif(Auth::user()->role == 'dapur')
                            <li><a href="{{ route('dapur.dashboard') }}" class="hover:text-indigo-600 transition">Monitor Dapur</a></li>
                        @endif
                    @endauth
                </ul>
            </div>

            <!-- Bagian 3: Kontak & Sosmed -->
            <div class="flex flex-col space-y-4">
                <h3 class="text-lg font-bold text-gray-800">Hubungi Kami</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-indigo-500 w-5"></i>
                        Jl. Hang Tuah No. 8, Madras Hulu, Medan Polonia, Medan, Sumatera Utara
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-envelope text-indigo-500 w-5"></i>
                        support@kantinjaya.com
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-phone text-indigo-500 w-5"></i>
                        +62 895-0915-8681
                    </li>
                </ul>
                
                <!-- Social Media Icons -->
                <div class="flex space-x-4 mt-2">
                    <a href="https://www.instagram.com/krisjon_04" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-indigo-600 hover:text-white transition duration-300">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/krisjon_04" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-pink-600 hover:text-white transition duration-300">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.instagram.com/krisjon_04" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-blue-400 hover:text-white transition duration-300">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="border-t border-gray-200 mt-8 pt-8 text-center">
            <p class="text-sm text-gray-400">
                &copy; {{ date('Y') }} <span class="font-bold text-indigo-600">Kantin Jaya</span>. All rights reserved.
                <span class="mx-2">|</span>
                Dibuat dengan ❤️ oleh Mahasiswa PTI
            </p>
        </div>
    </div>
</footer>