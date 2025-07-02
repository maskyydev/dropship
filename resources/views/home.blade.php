@extends('layouts.app')

@section('content')
<div class="bg-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Temukan Produk Terbaik untuk Bisnis Anda</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Platform dropship terlengkap dengan ribuan produk dari supplier terpercaya</p>
        </div>

        <!-- Product Grid -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Produk Terbaru</h2>
            
            <!-- Search and Filter Form -->
            <form method="GET" action="{{ route('home') }}">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div class="mb-4 md:mb-0">
                        <input type="text" name="search" placeholder="Cari produk..." 
                            value="{{ $search ?? '' }}"
                            class="px-4 py-2 border border-gray-300 rounded-lg w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="all" {{ $selectedCategory == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ $selectedCategory == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>

                        <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="latest" {{ $selectedSort == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_high" {{ $selectedSort == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="price_low" {{ $selectedSort == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                        </select>

                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                            Filter
                        </button>

                        @if($search || $selectedCategory != 'all' || $selectedSort != 'latest')
                            <a href="{{ route('home') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Products -->
            @if($products->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach($products as $product)
                     <div class="bg-white rounded-lg border border-gray-300 shadow-sm hover:shadow-lg overflow-hidden transition-all duration-200 relative">
                        <a href="{{ route('products.show', $product) }}">
                            <div class="aspect-square bg-gray-100 overflow-hidden">
                                @php
                                    $firstImage = $product->images->first();
                                    $imagePath = $firstImage ? 'storage/products/' . $firstImage->filename : null;
                                @endphp

                                @if($imagePath && file_exists(public_path($imagePath)))
                                    <img src="{{ asset($imagePath) }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                @else
                                    <img src="{{ asset('images/default-product.png') }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                @endif
                            </div>
                        </a>
                        <div class="p-3">
                            <div class="mb-2">
                                <a href="{{ route('products.show', $product) }}" class="font-medium text-sm line-clamp-2 hover:text-blue-600">
                                    {{ $product->name }}
                                </a>
                            </div>

                            {{-- Harga Modal dan Rekomendasi --}}
                            <div class="text-xs text-gray-600 leading-snug mb-2">
                                @php
                                    $hargaModal1 = $product->price;
                                    $hargaModal2 = $product->recommend_percent 
                                        ? $hargaModal1 + ($hargaModal1 * $product->recommend_percent / 100)
                                        : null;
                                @endphp
                                <div>Harga Modal:<br>Rp{{ number_format($hargaModal1, 0, ',', '.') }}</div>
                                @if($hargaModal2)
                                    <div class="mt-1">Rekomendasi Jual:<br><span class="text-green-700 font-semibold">Rp{{ number_format($hargaModal2, 0, ',', '.') }}</span></div>
                                @endif
                            </div>

                            <div class="flex justify-between items-center mb-2">
                                <span class="font-bold text-gray-900 text-base">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                                <span class="text-gray-500 text-xs">Stok: {{ $product->stock }}</span>
                            </div>

                            {{-- Kabupaten --}}
                            @php
                                $kabupaten = '';
                                if (!empty($product->alamat)) {
                                    $parts = explode(',', $product->alamat);
                                    $kabupaten = isset($parts[1]) ? trim($parts[1]) : '';
                                }
                            @endphp
                            @if($kabupaten)
                                <div class="flex justify-end items-center text-[11px] text-gray-500 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-red-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 2a6 6 0 00-6 6c0 4.418 6 10 6 10s6-5.582 6-10a6 6 0 00-6-6zm0 8a2 2 0 110-4 2 2 0 010 4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $kabupaten }}
                                </div>
                            @endif

                            {{-- Tombol Aksi --}}
                            <div class="flex justify-between items-center gap-2">
                                @auth
                                    @php
                                        $userId = auth()->id();
                                        $isAlreadySold = \App\Models\ProductJual::where('user_id', $userId)
                                            ->where('product_id', $product->id)
                                            ->where('filter', 'jual')
                                            ->exists();
                                    @endphp

                                    @if($isAlreadySold)
                                        <button type="button"
                                            class="w-full border border-gray-400 text-gray-400 px-2 py-1 rounded text-sm flex items-center justify-center cursor-not-allowed"
                                            disabled>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Ditambahkan
                                        </button>
                                    @else
                                        <form action="{{ route('product.jual', $product->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit"
                                                class="w-full border border-blue-600 text-blue-600 px-2 py-1 rounded text-sm hover:bg-blue-50 flex items-center justify-center transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Jual Sekarang
                                            </button>
                                        </form>
                                    @endif

                                    @php
                                        // Cek apakah produk sudah ada di wishlist user yang sedang login
                                        $isWishlisted = \App\Models\ProductJual::where('user_id', auth()->id())
                                            ->where('product_id', $product->id)
                                            ->where('filter', 'wishlist') // ← perbaiki dari "withlist" ke "wishlist"
                                            ->exists();
                                    @endphp

                                    <form action="{{ route('product.wishlist', $product->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="transition {{ $isWishlisted ? 'text-red-500 hover:text-red-600' : 'text-gray-500 hover:text-red-500' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 18.343 3.172 11.515a4 4 0 010-5.656z"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <div class="flex items-center gap-2 w-full">
                                        <a href="{{ route('login') }}"
                                            class="flex-1 border border-blue-600 text-blue-600 px-2 py-1 rounded text-sm hover:bg-blue-50 flex items-center justify-center transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Jual Sekarang
                                        </a>
                                        <a href="{{ route('login') }}" class="text-gray-500 hover:text-red-500 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 18.343 3.172 11.515a4 4 0 010-5.656z"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white p-8 rounded-lg text-center border">
                    <p class="text-gray-500">Tidak ada produk ditemukan.</p>
                </div>
            @endif


            @if($products->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach($products as $product)
                        {{-- ... tampilkan produk ... --}}
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                @endif

            @else
                <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                    <p class="text-gray-500">Tidak ada produk yang ditemukan</p>
                    <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Reset filter</a>
                </div>
            @endif

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->appends([
                    'search' => $search,
                    'category' => $selectedCategory,
                    'sort' => $selectedSort
                ])->links() }}
            </div>
        </div>

        <!-- Features Section -->
        <div class="py-12">
            <h2 class="text-2xl font-bold text-center text-gray-900 mb-8">Mengapa Memilih Kami?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="text-indigo-600 mb-4 mx-auto w-12 h-12 flex items-center justify-center rounded-full bg-indigo-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Produk Berkualitas</h3>
                    <p class="text-gray-600">Hanya produk dengan kualitas terbaik dari supplier terpercaya</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="text-indigo-600 mb-4 mx-auto w-12 h-12 flex items-center justify-center rounded-full bg-indigo-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Pengiriman Cepat</h3>
                    <p class="text-gray-600">Proses pengiriman cepat dengan berbagai pilihan ekspedisi</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="text-indigo-600 mb-4 mx-auto w-12 h-12 flex items-center justify-center rounded-full bg-indigo-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Harga Kompetitif</h3>
                    <p class="text-gray-600">Harga dropship terbaik dengan margin keuntungan menarik</p>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')
@endsection