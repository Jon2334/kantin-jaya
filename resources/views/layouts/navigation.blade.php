<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex flex-1">
                <div class="shrink-0 flex items-center">
                    <a href="#">
                        <h1 class="font-bold text-xl text-indigo-600">KANTIN JAYA</h1>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(Auth::user()->role == 'kasir')
                        <x-nav-link :href="route('kasir.dashboard')" :active="request()->routeIs('kasir.dashboard')">{{ __('Dashboard Kasir') }}</x-nav-link>
                        <x-nav-link :href="route('kasir.items.index')" :active="request()->routeIs('kasir.items.*')">{{ __('Kelola Stok') }}</x-nav-link>
                    @endif

                    @if(Auth::user()->role == 'dapur')
                        <x-nav-link :href="route('dapur.dashboard')" :active="request()->routeIs('dapur.dashboard')">{{ __('Monitor Pesanan') }}</x-nav-link>
                    @endif

                    @if(Auth::user()->role == 'pembeli')
                        <x-nav-link :href="route('pembeli.dashboard')" :active="request()->routeIs('pembeli.dashboard')">{{ __('Pesan Makanan') }}</x-nav-link>
                    @endif

                    @if(Auth::user()->role == 'supplier')
                        <x-nav-link :href="route('supplier.dashboard')" :active="request()->routeIs('supplier.dashboard')">{{ __('Dashboard Supplier') }}</x-nav-link>
                    @endif
                </div>

                @if(Auth::user()->role == 'pembeli')
                <div class="hidden sm:flex items-center ms-6 flex-1 max-w-md">
                    <form action="{{ route('search') }}" method="GET" class="w-full">
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                </svg>
                            </div>
                            <input type="search" name="query" class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Cari menu..." required>
                        </div>
                    </form>
                </div>
                @endif
                </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        
        @if(Auth::user()->role == 'pembeli')
        <div class="pt-2 pb-1 px-4 border-b border-gray-200">
            <form action="{{ route('search') }}" method="GET">
                <input type="search" name="query" class="block w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50" placeholder="Cari menu...">
            </form>
        </div>
        @endif
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->role == 'kasir')
                <x-responsive-nav-link :href="route('kasir.dashboard')" :active="request()->routeIs('kasir.dashboard')">{{ __('Dashboard Kasir') }}</x-responsive-nav-link>
            @endif
            @if(Auth::user()->role == 'pembeli')
                <x-responsive-nav-link :href="route('pembeli.dashboard')" :active="request()->routeIs('pembeli.dashboard')">{{ __('Pesan Makanan') }}</x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>