@extends('layouts.app')

@section('content')
<div class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                        <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                        Beranda
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <a href="#" class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2">{{ $product->category }}</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $product->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Product Detail -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8 border border-gray-100">
            <div class="md:flex">
                <!-- Product Images -->
                <div class="md:w-1/2 p-6">
                    <div class="sticky top-4">
                        <!-- Main Image -->
                        <div class="mb-4 h-80 bg-gray-50 rounded-lg overflow-hidden flex items-center justify-center">
                            @if($product->images->isNotEmpty())
                                <img id="mainImage" src="{{ asset('storage/products/' . $product->images[0]->filename) }}"
                                    alt="{{ $product->name }}"
                                    class="max-h-full max-w-full object-contain cursor-zoom-in"
                                    onclick="openImageModal('{{ asset('storage/products/' . $product->images[0]->filename) }}')">
                            @else
                                <img id="mainImage" src="{{ asset('images/default-product.png') }}"
                                    alt="Default Image"
                                    class="max-h-full max-w-full object-contain">
                            @endif
                        </div>
                        
                        <!-- Thumbnails -->
                        <div class="grid grid-cols-4 gap-3">
                            @forelse ($product->images as $key => $image)
                                <div class="h-20 bg-gray-50 rounded-md border border-gray-200 overflow-hidden cursor-pointer hover:border-indigo-300 transition-colors"
                                    onclick="changeMainImage('{{ asset('storage/products/' . $image->filename) }}', this)">
                                    <img src="{{ asset('storage/products/' . $image->filename) }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover">
                                </div>
                            @empty
                                @for($i = 0; $i < 4; $i++)
                                    <div class="h-20 bg-gray-50 rounded-md border border-gray-200 overflow-hidden">
                                        <img src="{{ asset('images/default-product.png') }}"
                                            alt="Default Image"
                                            class="w-full h-full object-cover">
                                    </div>
                                @endfor
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="md:w-1/2 p-6 border-l border-gray-100">
                    <div class="space-y-5">
                        <!-- Title and Category -->
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 leading-tight">{{ $product->name }}</h1>
                            <div class="flex items-center mt-2 space-x-2">
                                <span class="bg-indigo-50 text-indigo-700 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $product->category }}</span>
                                <span class="text-sm text-gray-500">• Stok: {{ $product->stock }}</span>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="flex items-baseline space-x-2">
                            <span class="text-3xl font-bold text-gray-900">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                            <span class="text-sm text-gray-500">/pcs</span>
                        </div>

                        <!-- Description -->
                        <div class="prose prose-sm max-w-none text-gray-600">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Deskripsi Produk</h3>
                            <p>{{ $product->description ?? 'Tidak ada deskripsi produk' }}</p>
                        </div>

                        <!-- Quantity and Buy Now -->
                        <div class="pt-2">
                            <div class="flex items-center space-x-4">
                                <!-- <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                    <button onclick="decrementQuantity()" class="bg-gray-100 text-gray-600 hover:bg-gray-200 px-3 py-2 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <input type="number" id="quantity" value="1" min="1" max="{{ $product->stock }}" 
                                        class="w-12 text-center border-x border-gray-300 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <button onclick="incrementQuantity()" class="bg-gray-100 text-gray-600 hover:bg-gray-200 px-3 py-2 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </div> -->
                                
                                @auth
                                    @php
                                        $userId = auth()->id();
                                        $isAlreadySold = \App\Models\ProductJual::where('user_id', $userId)
                                            ->where('product_id', $product->id)
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

                                    {{-- Wishlist --}}
                                    <form action="#" method="POST">
                                        @csrf
                                        <button type="submit" class="text-gray-500 hover:text-red-500 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 18.343 3.172 11.515a4 4 0 010-5.656z"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition-colors font-medium flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                        Login untuk Membeli
                                    </a>
                                @endauth
                            </div>
                        </div>

                        <!-- Marketing Kit + Preview Landing Page -->
                        @auth
                        @php
                            $files = $product->marketing_files ?? collect();
                            $preview = $product->preview ?? null;

                            $fileTypes = [
                                'pdf' => 'PDF',
                                'ppt' => 'PPT',
                                'pptx' => 'PPTX',
                                'doc' => 'DOC',
                                'docx' => 'DOCX',
                                'xls' => 'XLS',
                                'xlsx' => 'XLSX',
                            ];
                        @endphp

                        <div class="pt-4 mt-4 border-t border-gray-200 space-y-6">
                            <!-- Marketing Kit Section -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Marketing Kit</h3>

                                @if($files->count())
                                    <div class="grid grid-cols-2 gap-3">
                                        @foreach($files as $file)
                                            @php
                                                $ext = strtolower(pathinfo($file->filename, PATHINFO_EXTENSION));
                                                $label = $fileTypes[$ext] ?? strtoupper($ext);
                                                $icon = match($ext) {
                                                    'pdf' => 'M7 21h10a2 2 0 002-2V9.414l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                                                    'ppt', 'pptx' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414V19a2 2 0 01-2 2z',
                                                    'doc', 'docx' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                                                    'xls', 'xlsx' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10',
                                                    default => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'
                                                };
                                            @endphp
                                            <a href="{{ Storage::url('marketing-kit/' . $file->filename) }}"
                                            target="_blank"
                                            class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors flex items-center">
                                                <svg class="h-6 w-6 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                                                </svg>
                                                <span class="text-sm">Download {{ $label }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 italic">Marketing Kit belum ditambahkan.</p>
                                @endif
                            </div>

                            <!-- Preview Landing Page Section -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Preview Landing Page</h3>

                                @if($preview && $preview->url)
                                    <a href="{{ $preview->url }}" target="_blank"
                                    class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 3h7v7m0 0L10 21l-7-7L21 3z" />
                                        </svg>
                                        Lihat Preview Landing Page
                                    </a>
                                @else
                                    <p class="text-sm text-gray-500 italic">Preview Landing Page belum tersedia.</p>
                                @endif
                            </div>
                        </div>
                        @endauth

                        <!-- Product Meta -->
                        <div class="pt-4 mt-4 border-t border-gray-200 space-y-3">
                            <!-- <div class="flex items-center text-sm text-gray-600">
                                <svg class="flex-shrink-0 h-5 w-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                                <span>Gratis ongkir untuk pembelian di atas Rp100.000</span>
                            </div> -->
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="flex-shrink-0 h-5 w-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Pengiriman dalam 1-3 hari kerja</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Modal -->
        <div id="imageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-75">
            <div class="relative max-w-4xl w-full">
                <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <img id="modalImage" src="" alt="" class="w-full max-h-screen object-contain">
            </div>
        </div>

        <script>
            // Quantity control
            function incrementQuantity() {
                const quantityInput = document.getElementById('quantity');
                const max = parseInt(quantityInput.max);
                let value = parseInt(quantityInput.value);
                if (value < max) {
                    quantityInput.value = value + 1;
                    document.getElementById('hiddenQuantity').value = quantityInput.value;
                }
            }

            function decrementQuantity() {
                const quantityInput = document.getElementById('quantity');
                let value = parseInt(quantityInput.value);
                if (value > 1) {
                    quantityInput.value = value - 1;
                    document.getElementById('hiddenQuantity').value = quantityInput.value;
                }
            }

            // Update hidden quantity when input changes
            document.getElementById('quantity').addEventListener('change', function() {
                let value = parseInt(this.value);
                const max = parseInt(this.max);
                const min = parseInt(this.min);
                
                if (isNaN(value) || value < min) {
                    this.value = min;
                } else if (value > max) {
                    this.value = max;
                }
                
                document.getElementById('hiddenQuantity').value = this.value;
            });

            // Image gallery functionality
            function changeMainImage(src, element) {
                document.getElementById('mainImage').src = src;
                // Remove active class from all thumbnails
                document.querySelectorAll('.grid.grid-cols-4 div').forEach(thumb => {
                    thumb.classList.remove('border-indigo-500');
                    thumb.classList.add('border-gray-200');
                });
                // Add active class to clicked thumbnail
                element.classList.remove('border-gray-200');
                element.classList.add('border-indigo-500');
            }

            // Image modal functionality
            function openImageModal(src) {
                document.getElementById('modalImage').src = src;
                document.getElementById('imageModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeImageModal() {
                document.getElementById('imageModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // Close modal when clicking outside the image
            document.getElementById('imageModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeImageModal();
                }
            });
        </script>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Produk Terkait</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <a href="{{ route('products.show', $related) }}">
                        <div class="h-48 overflow-hidden">
                            <img src="{{ $related->image ? asset('storage/'.$related->image) : asset('images/default-product.png') }}" 
                                 alt="{{ $related->name }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    </a>
                    <div class="p-4">
                        <a href="{{ route('products.show', $related) }}" class="font-semibold text-lg hover:text-indigo-600 block">{{ $related->name }}</a>
                        <p class="text-gray-500 text-sm mt-1">{{ $related->category }}</p>
                        <div class="mt-3 flex justify-between items-center">
                            <span class="font-bold text-gray-900">Rp{{ number_format($related->price, 0, ',', '.') }}</span>
                            @auth
                                <form action="{{ route('cart.add', $related) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded-md text-sm hover:bg-indigo-700 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Beli
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-3 py-1 rounded-md text-sm hover:bg-indigo-700 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Beli
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<footer class="bg-gray-100 text-gray-700 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- First Column -->
            <div>
                <h2 class="text-2xl font-bold mb-4">Dropship Solution</h2>
                <p class="text-sm mb-4">
                    Dropship Solution merupakan platform penyedia produk untuk berjualan tanpa modal, tanpa ribet packing dan tanpa risiko.
                </p>
                <p class="text-sm">
                    PT. Dropship Solution Indonesia<br>
                    © 2023. DropshipSolution.id
                </p>
            </div>

            <!-- Second Column -->
            <div>
                <h3 class="font-bold text-lg mb-4">Support</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-blue-600 text-sm">Help Center</a></li>
                    <li><a href="#" class="hover:text-blue-600 text-sm">Privacy & Policy</a></li>
                    <li><a href="#" class="hover:text-blue-600 text-sm">Terms & Conditions Seller</a></li>
                </ul>
            </div>

            <!-- Third Column -->
            <div>
                <h3 class="font-bold text-lg mb-4">Contact</h3>
                <address class="not-italic text-sm">
                    <p class="mb-2">G84C+GP9, Kedung Papar, Kedung Mlati,</p>
                    <p class="mb-2">Kec. Kesamben, Kabupaten Jombang,</p>
                    <p class="mb-2">Jawa Timur 61484</p>
                    <p class="mb-2">Email: example@dropshipsolution.id</p>
                    <p class="mb-2">Phone: +62 812-2200-1577</p>
                    <p>Phone Alt: +62 812-2200-1554</p>
                </address>
            </div>
        </div>

        <div class="border-t border-gray-300 mt-8 pt-6 text-center text-sm">
            <p>© 2023 Dropship Solution. All rights reserved.</p>
        </div>
    </div>
</footer>
@endsection