@extends('layouts.admin')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-6">Tambah Produk Baru</h2>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Produk -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Kategori -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                <select name="category" id="category" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Pilih Kategori</option>
                    @foreach(\App\Models\Product::$categories as $category)
                        <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                    <option value="__new__" {{ old('category') == '__new__' ? 'selected' : '' }}>+ Tambah Kategori Baru</option>
                </select>

                {{-- Input Kategori Baru --}}
                <div id="new-category-wrapper" class="{{ old('category') == '__new__' ? '' : 'hidden' }} mt-2">
                    <input type="text" name="new_category" id="new_category" placeholder="Tulis kategori baru..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        value="{{ old('new_category') }}">
                </div>

                @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                @error('new_category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <script>
                const selectEl = document.getElementById('category');
                const newCategoryWrapper = document.getElementById('new-category-wrapper');
                const newCategoryInput = document.getElementById('new_category');

                selectEl.addEventListener('change', function () {
                    if (this.value === '__new__') {
                        newCategoryWrapper.classList.remove('hidden');
                        newCategoryInput.required = true;
                    } else {
                        newCategoryWrapper.classList.add('hidden');
                        newCategoryInput.required = false;
                    }
                });
            </script>

            <!-- Harga -->
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) *</label>
                <input type="number" name="price" id="price" value="{{ old('price') }}" min="0" step="0.01" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Rekomendasi Jual -->
            <div>
                <label for="recommend_percent" class="block text-sm font-medium text-gray-700 mb-1">
                    Rekomendasi Jual (% Margin)
                </label>
                <div class="flex items-center space-x-2">
                    <input type="number" name="recommend_percent" id="recommend_percent" value="{{ old('recommend_percent') }}"
                        min="0" max="100" step="0.01"
                        class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Contoh: 10 untuk 10%</span>
                </div>

                <!-- Total harga rekomendasi akan tampil di sini -->
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
                    const base = parseFloat(priceInput.value);
                    const percent = parseFloat(percentInput.value);
                    if (!isNaN(base) && !isNaN(percent)) {
                        const total = base + (base * percent / 100);
                        output.textContent = 'Rp ' + total.toLocaleString('id-ID');
                    } else {
                        output.textContent = 'Rp -';
                    }
                }

                priceInput.addEventListener('input', updateRecommendPrice);
                percentInput.addEventListener('input', updateRecommendPrice);

                // Panggil saat load awal
                updateRecommendPrice();
            </script>

            <!-- Stok -->
            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stok *</label>
                <input type="number" name="stock" id="stock" value="{{ old('stock') }}" min="0" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('stock') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Barcode -->
            <div>
                <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('barcode') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Alamat -->
            <div class="md:col-span-2">
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat Perangkat</label>
                <input type="text" name="alamat" id="alamat" value="{{ old('alamat') }}" readonly
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Gambar Produk -->
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
                <input type="file" name="images[]" id="images" accept="image/*" multiple
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('images') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Marketing Kit Upload -->
            <div>
                <label for="marketing_files" class="block text-sm font-medium text-gray-700 mb-1">Marketing Kit</label>
                <input type="file" name="marketing_files[]" id="marketing_files" multiple
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('marketing_files') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Preview URLs -->
            <div class="md:col-span-2">
                <label for="preview_urls" class="block text-sm font-medium text-gray-700 mb-1">Preview Landing Page</label>
                <textarea name="preview_urls" id="preview_urls" rows="3" placeholder="Pisahkan dengan koma atau enter"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('preview_urls') }}</textarea>
                @error('preview_urls') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Deskripsi -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Berat -->
            <div>
                <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Berat (gram)</label>
                <input type="number" name="weight" id="weight" value="{{ old('weight') }}" min="0" step="0.01"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('weight') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Dimensi -->
            <div>
                <label for="dimensions" class="block text-sm font-medium text-gray-700 mb-1">Dimensi (P x L x T cm)</label>
                <input type="text" name="dimensions" id="dimensions" value="{{ old('dimensions') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('dimensions') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('admin.products.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors">Batal</a>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors">Simpan Produk</button>
        </div>
    </form>
</div>

<script>
    const priceInput = document.getElementById('price');
    const percentInput = document.getElementById('recommend_percent');
    const output = document.getElementById('recommended_price_display');

    function updateRecommendPrice() {
        const base = parseFloat(priceInput.value);
        const percent = parseFloat(percentInput.value);
        if (!isNaN(base) && !isNaN(percent)) {
            const total = base + (base * percent / 100);
            output.textContent = 'Harga Rekomendasi: Rp ' + total.toFixed(0);
        } else {
            output.textContent = '';
        }
    }

    priceInput.addEventListener('input', updateRecommendPrice);
    percentInput.addEventListener('input', updateRecommendPrice);

    // Ambil lokasi GPS otomatis
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
