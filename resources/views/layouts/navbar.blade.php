<!-- Navbar -->
<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            
            <!-- Kiri: Logo dan Home -->
            <div class="flex items-center space-x-4">
                <!-- Judul -->
                <h1 class="text-xl font-bold text-indigo-600">Dropship Solution</h1>
            </div>

            <!-- Kanan: Ikon & Auth -->
            <div class="flex items-center space-x-6">
                <!-- Icon Home -->
                <a href="{{ url('/') }}" class="text-gray-500 hover:text-blue-600" title="Beranda">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9-7 9 7v10a1 1 0 01-1 1h-6m-6 0H4a1 1 0 01-1-1V10z" />
                    </svg>
                </a>

                <!-- Produk -->
                <a href="{{ route('produk.daftar') }}" class="text-gray-500 hover:text-blue-600" title="Daftar Produk">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </a>

                <!-- Wishlist -->
                <a href="{{ route('produk.wishlist') }}" class="text-gray-500 hover:text-blue-600" title="Wishlist">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </a>

                <!-- User Dropdown -->
                @auth
                <div class="ml-3 relative" title="Akun Saya">
                    <div class="flex items-center">
                        <button type="button" class="bg-gray-100 rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="user-menu-button">
                            <span class="sr-only">Open user menu</span>
                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-500">
                                <span class="text-sm font-medium text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </span>
                        </button>
                    </div>

                    <!-- Dropdown -->
                    <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5" role="menu" id="user-menu">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left" role="menuitem">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-blue-600 text-sm font-medium" title="Masuk">Login</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm font-medium hover:bg-blue-700" title="Daftar">Register</a>
                </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Dropdown script -->
<script>
    document.getElementById('user-menu-button')?.addEventListener('click', function() {
        const menu = document.getElementById('user-menu');
        menu.classList.toggle('hidden');
    });
</script>
