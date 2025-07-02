@extends('layouts.admin')

@section('content')
@php
    use App\Models\Product;
@endphp
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-6">Edit Produk: {{ $product->name }}</h2>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Nama Produk --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Kategori --}}
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                <select name="category" id="category" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Pilih Kategori</option>
                    @foreach(Product::$categories as $category)
                        <option value="{{ $category }}" {{ old('category', $product->category) == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                    <option value="__new__" {{ old('category') == '__new__' ? 'selected' : '' }}>+ Tambah Kategori Baru</option>
                </select>

                {{-- Input Kategori Baru (Hidden by Default) --}}
                <div id="new-category-wrapper" class="{{ old('category') == '__new__' ? '' : 'hidden' }} mt-2">
                    <input type="text" name="new_category" id="new_category" placeholder="Tulis kategori baru..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        value="{{ old('new_category') }}">
                </div>

                @error('category') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('new_category') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <script>
                const categorySelect = document.getElementById('category');
                const newCategoryInput = document.getElementById('new_category');
                const newCategoryWrapper = document.getElementById('new-category-wrapper');

                categorySelect.addEventListener('change', function () {
                    if (this.value === '__new__') {
                        newCategoryWrapper.classList.remove('hidden');
                        newCategoryInput.required = true;
                    } else {
                        newCategoryWrapper.classList.add('hidden');
                        newCategoryInput.required = false;
                    }
                });
            </script>

            {{-- Harga --}}
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) *</label>
                <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" min="0" step="0.01" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('price') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Rekomendasi Jual (Persen) --}}
            <div>
                <label for="recommend_percent" class="block text-sm font-medium text-gray-700 mb-1">
                    Rekomendasi Jual (% Margin)
                </label>
                <div class="flex items-center space-x-2">
                    <input type="number" name="recommend_percent" id="recommend_percent"
                        value="{{ old('recommend_percent', $product->recommend_percent) }}" min="0" max="100" step="0.01"
                        class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Contoh: 10 untuk 10%</span>
                </div>

                <!-- Hasil perhitungan total harga jual -->
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Total harga jual dengan margin:</p>
                    <p id="recommended_price_display" class="text-lg font-semibold text-green-700">Rp -</p>
                </div>
            </div>

            <script>
                const priceInput = document.getElementById('price');
                const percentInput = document.getElementById('recommend_percent');
                const output = document.getElementById('recommended_price_display');

                function updateRecommendPrice() {
                    const base = parseFloat(priceInput?.value);
                    const percent = parseFloat(percentInput?.value);
                    if (!isNaN(base) && !isNaN(percent)) {
                        const total = base + (base * percent / 100);
                        output.textContent = 'Rp ' + total.toLocaleString('id-ID');
                    } else {
                        output.textContent = 'Rp -';
                    }
                }

                if (priceInput && percentInput && output) {
                    priceInput.addEventListener('input', updateRecommendPrice);
                    percentInput.addEventListener('input', updateRecommendPrice);
                    // Jalankan saat halaman pertama kali dimuat
                    window.addEventListener('DOMContentLoaded', updateRecommendPrice);
                }
            </script>

            {{-- Stok --}}
            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stok *</label>
                <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" min="0" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('stock') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Barcode --}}
            <div>
                <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $product->barcode) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('barcode') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Alamat (otomatis via GPS) --}}
            <div class="md:col-span-2">
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat Perangkat</label>
                <input type="text" name="alamat" id="alamat" value="{{ old('alamat', $product->alamat) }}" readonly
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Upload Gambar --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
                @if($product->images && $product->images->count())
                    <div class="flex flex-wrap gap-4 mb-4">
                        @foreach($product->images as $img)
                            <div class="relative w-24 h-24 group">
                                <img src="{{ asset('storage/products/' . $img->filename) }}"
                                    class="object-cover w-full h-full rounded border">
                                <label class="absolute -top-2 -right-2 bg-white border border-gray-300 rounded-full shadow p-1 cursor-pointer">
                                    <input type="checkbox" name="remove_images[]" value="{{ $img->id }}" class="hidden">
                                    <span class="text-xs text-red-600">✕</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif
                <input type="file" name="images[]" multiple accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('images.*') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Upload File Marketing Kit --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">File Marketing Kit (PDF, DOC, dll)</label>
                @if($product->files && $product->files->count())
                    <ul class="list-disc ml-6 mb-3 text-sm text-gray-800 space-y-1">
                        @foreach($product->files as $file)
                            <li class="flex justify-between items-center">
                                <a href="{{ asset('storage/product/kit/' . $file->filename) }}"
                                target="_blank" class="text-blue-600 underline">{{ $file->filename }}</a>
                                <label class="ml-2 text-red-600 flex items-center space-x-1">
                                    <input type="checkbox" name="remove_files[]" value="{{ $file->id }}">
                                    <span>Hapus</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <input type="file" name="marketing_files[]" multiple accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('marketing_files.*') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>


            {{-- URL Preview --}}
            <div class="md:col-span-2">
                <label for="preview_urls" class="block text-sm font-medium text-gray-700 mb-1">Preview URL</label>
                <textarea name="preview_urls" id="preview_urls" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('preview_urls', $product->previews->pluck('url')->implode("n")) }}</textarea>
            </div>

            {{-- Deskripsi --}}
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('admin.products.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors">Batal</a>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">Update Produk</button>
        </div>
    </form>
</div>

<script>
    const priceInput = document.getElementById('price');
    const percentInput = document.getElementById('recommend_percent');
    const output = document.getElementById('recommend_price');

    function updateRecommendPrice() {
        const base = parseFloat(priceInput.value);
        const percent = parseFloat(percentInput.value);
        if (!isNaN(base) && !isNaN(percent)) {
            const total = base + (base * percent / 100);
            output.textContent = total.toFixed(0);
        }
    }

    priceInput.addEventListener('input', updateRecommendPrice);
    percentInput.addEventListener('input', updateRecommendPrice);

    // Ambil lokasi otomatis saat load
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(async function(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`);
                const data = await response.json();
                document.getElementById('alamat').value = data.display_name || `${lat}, ${lon}`;
            } catch (err) {
                document.getElementById('alamat').value = `${lat}, ${lon}`;
            }
        });
    } else {
        document.getElementById('alamat').value = 'Lokasi tidak tersedia';
    }
</script>
@endsection
