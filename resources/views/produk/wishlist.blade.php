@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar Filters -->
        <div class="w-full md:w-64 flex-shrink-0">
            <form method="GET" action="{{ url()->current() }}" class="bg-white rounded-lg shadow border border-gray-200">

                {{-- Filter Lokasi (Kabupaten) --}}
                <div x-data="{ open: window.innerWidth >= 768 }" class="border-b border-gray-200">
                    <button type="button" @click="open = !open"
                        class="w-full flex justify-between items-center px-4 py-3 text-left font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                        <span>Lokasi</span>
                        <svg :class="{ 'transform rotate-180': open }" class="h-5 w-5 text-gray-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="px-4 pb-3 space-y-2 max-h-60 overflow-y-auto">
                        @php
                            // Ambil kabupaten unik dari data alamat
                            $kabupatenList = $produk->pluck('alamat')
                                ->filter()
                                ->map(function($alamat) {
                                    $parts = explode(',', $alamat);
                                    return isset($parts[1]) ? trim($parts[1]) : null;
                                })
                                ->unique()
                                ->filter()
                                ->values();
                        @endphp

                        @foreach($kabupatenList as $kab)
                            <div class="flex items-center">
                                <input type="checkbox" id="location-{{ Str::slug($kab) }}" 
                                    name="locations[]" value="{{ $kab }}"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    {{ in_array($kab, request('locations', [])) ? 'checked' : '' }}>
                                <label for="location-{{ Str::slug($kab) }}" class="ml-2 text-sm text-gray-700">
                                    {{ $kab }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Filter Harga --}}
                <div x-data="{ open: window.innerWidth >= 768 }" class="border-b border-gray-200">
                    <button type="button" @click="open = !open"
                        class="w-full flex justify-between items-center px-4 py-3 text-left font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                        <span>Harga</span>
                        <svg :class="{ 'transform rotate-180': open }" class="h-5 w-5 text-gray-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="px-4 pb-3 space-y-2">
                        @foreach([
                            '< Rp500.000' => [0, 500000],
                            'Rp500.000 - Rp1.000.000' => [500000, 1000000],
                            'Rp1.000.000 - Rp3.000.000' => [1000000, 3000000],
                            'Rp3.000.000 - Rp5.000.000' => [3000000, 5000000],
                            '> Rp5.000.000' => [5000000, null]
                        ] as $label => $range)
                        <div class="flex items-center">
                            <input type="checkbox" id="price-{{ Str::slug($label) }}" 
                                name="price_ranges[]" value="{{ implode(',', $range) }}"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                {{ collect(request('price_ranges'))->contains(implode(',', $range)) ? 'checked' : '' }}>
                            <label for="price-{{ Str::slug($label) }}" class="ml-2 text-sm text-gray-700">
                                {{ $label }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Tombol Reset --}}
                <div class="p-4">
                    <a href="{{ url()->current() }}" class="mt-2 block text-center text-sm text-blue-600 hover:text-blue-800">
                        Reset Semua Filter
                    </a>
                </div>
            </form>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header and Search -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h1 class="text-2xl font-bold text-gray-800">Wishlist Saya</h1>
                
                <form method="GET" action="{{ route('produk.wishlist') }}" class="relative w-full md:w-64">
                    <input type="text" name="cari" placeholder="Cari produk..." 
                           value="{{ request('cari') }}"
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>
            </div>

            <!-- Products Grid -->
            @if($produk->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($produk as $item)
                <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 hover:shadow-md transition-shadow">
                    <!-- Product Image -->
                    <div class="h-48 bg-gray-100 relative overflow-hidden">
                        @if($item->product && $item->product->images->count() > 0)
                            <img src="{{ asset('storage/products/' . $item->product->images->first()->filename) }}" 
                                alt="{{ $item->name }}" 
                                class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('images/default-product.png') }}" 
                                alt="{{ $item->name }}" 
                                class="w-full h-full object-cover">
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="p-4 text-sm relative">
                        <!-- Nama Produk -->
                        <h3 class="text-black font-bold mb-1">{{ $item->name }}</h3>

                        <!-- COD -->
                        <!-- <p class="text-xs text-green-600 font-bold mb-1">COD</p> -->

                        <!-- Harga Modal -->
                        <p class="text-gray-900">Harga Modal:</p>
                        <p class="text-orange-600 font-bold mb-1">Rp{{ number_format($item->price, 0, ',', '.') }}</p>

                        <!-- Rekomendasi Harga Jual -->
                        @php
                            $hargaJual = $item->price + ($item->price * ($item->recommend_percent ?? 0) / 100);
                        @endphp
                        <p class="text-gray-500">Rekomendasi Harga Jual:</p>
                        <p class="text-green-800 font-semibold mb-2">Rp{{ number_format($hargaJual, 0, ',', '.') }}</p>

                        <!-- Lokasi dan Stok -->
                        @php
                            $kabupaten = '';
                            if (!empty($item->alamat)) {
                                $parts = explode(',', $item->alamat);
                                $kabupaten = isset($parts[1]) ? trim($parts[1]) : '';
                            }
                        @endphp
                        <div class="flex justify-between items-center text-xs text-gray-500 mt-4">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-red-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 2a6 6 0 00-6 6c0 4.418 6 10 6 10s6-5.582 6-10a6 6 0 00-6-6zm0 8a2 2 0 110-4 2 2 0 010 4z" clip-rule="evenodd" />
                                </svg>
                                {{ $kabupaten }}
                            </div>
                            <div class="text-right">
                                Stok: <span class="font-medium">{{ $item->stock }}</span>
                            </div>
                        </div>

                        <!-- Tombol Hapus dari Wishlist -->
                        <form action="{{ route('produk.hapus', $item->id) }}" method="POST" class="mt-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 text-white text-xs py-2 rounded hover:bg-red-700 transition">
                                Hapus dari Wishlist
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Wishlist Anda kosong</h3>
                <p class="mt-1 text-gray-500">Tambahkan produk ke wishlist untuk melihatnya di sini</p>
                <div class="mt-6">
                    <a href="{{ url('/') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 inline-block">
                        Jelajahi Produk
                    </a>
                </div>
            </div>
            @endif

            <!-- Pagination -->
            @if($produk->hasPages())
            <div class="mt-6">
                {{ $produk->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@include('layouts.footer')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle filter form submission
        const filterForm = document.querySelector('form[method="GET"]');
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    });
</script>
@endsection