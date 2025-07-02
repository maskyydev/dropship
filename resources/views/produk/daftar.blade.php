@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header dan Toolbar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Produk Saya</h1>
    </div>

    <!-- Session Messages -->
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded mb-4">
            {{ session('warning') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
            <div class="text-2xl font-bold text-blue-600">{{ $statistik['total'] }}</div>
            <div class="text-gray-600 text-sm">Total Produk</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
            <div class="text-2xl font-bold text-green-600">{{ $statistik['aktif'] }}</div>
            <div class="text-gray-600 text-sm">Produk Aktif</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
            <div class="text-2xl font-bold text-red-600">{{ $statistik['habis'] }}</div>
            <div class="text-gray-600 text-sm">Stok Habis</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
            <div class="text-2xl font-bold text-purple-600">{{ $statistik['pribadi'] }}</div>
            <div class="text-gray-600 text-sm">Produk Pribadi</div>
        </div>
    </div>

    <!-- Tabel Produk -->
    <form method="POST" action="{{ route('produk.bulk-delete') }}" id="produk-form">
        @csrf

        <!-- Bagian Atas: Tombol Aksi -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
            <!-- Tombol Aksi -->
            <div class="flex gap-2">
                <button type="submit" id="hapus-terpilih"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Hapus yang Dipilih
                </button>
            </div>

            <!-- Pencarian dan Filter -->
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <!-- Pencarian -->
                <div class="relative flex-grow">
                    <input type="text" name="cari" placeholder="Cari produk..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Kategori -->
                <select name="kategori" class="border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kategori)
                        <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                            {{ $kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tabel Produk -->
        <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 mb-4">
            <div class="px-6 py-3 bg-gray-50 border-b flex items-center">
                <div class="flex items-center mr-4">
                    <input id="select-all" type="checkbox"
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                </div>
                <div class="w-full grid grid-cols-12 gap-4">
                    <div class="col-span-4 font-medium text-gray-700">Produk</div>
                    <div class="col-span-2 font-medium text-gray-700 text-right">Harga</div>
                    <div class="col-span-2 font-medium text-gray-700 text-right">Jumlah</div>
                    <div class="col-span-2 font-medium text-gray-700 text-right">Stok</div>
                    <div class="col-span-2 font-medium text-gray-700 text-right">Subtotal</div>
                </div>
            </div>

            <div class="divide-y divide-gray-200" id="produk-list">
                @forelse($produk as $item)
                    <div class="px-6 py-4 hover:bg-gray-50 flex items-center produk-item"
                        x-data="{ checked: false, jumlah: 1, maxStock: {{ $item->stock }} }"
                        x-bind:class="{ 'bg-gray-100': checked }">
                        <div class="flex items-center mr-4">
                            <input type="checkbox" name="selected[]" value="{{ $item->id }}"
                                x-model="checked"
                                @change="updateTotalHarga()"
                                class="product-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        </div>
                        <div class="w-full grid grid-cols-12 gap-4 items-center">
                            <!-- Kolom Produk -->
                            <div class="col-span-4 flex items-center">
                                <div class="h-10 w-10 rounded-md overflow-hidden border mr-3">
                                    @php
                                        $firstImage = $item->product && $item->product->images->count() > 0
                                            ? $item->product->images->first()->filename
                                            : null;
                                    @endphp
                                    <img src="{{ $firstImage ? asset('storage/products/' . $firstImage) : asset('images/default-product.png') }}"
                                        alt="{{ $item->product->name ?? 'Produk' }}"
                                        class="h-full w-full object-cover">
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $item->name }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $item->product_id }}</div>
                                </div>
                            </div>
                            
                            <!-- Kolom Harga -->
                            <div class="col-span-2 text-right font-medium text-gray-900 harga-produk"
                                data-harga="{{ $item->price }}">
                                Rp{{ number_format($item->price, 0, ',', '.') }}
                            </div>
                            
                            <!-- Kolom Jumlah -->
                            <div class="col-span-2 text-right">
                                <div class="flex items-center justify-end">
                                    <button type="button" @click="if(jumlah > 1) jumlah--; updateTotalHarga()" 
                                        class="px-2 py-1 border border-gray-300 rounded-l bg-gray-100">
                                        -
                                    </button>
                                    <input type="number" x-model="jumlah" min="1" :max="maxStock"
                                        @change="updateTotalHarga()"
                                        class="w-12 text-center border-t border-b border-gray-300 py-1">
                                    <button type="button" @click="if(jumlah < maxStock) jumlah++; updateTotalHarga()" 
                                        class="px-2 py-1 border border-gray-300 rounded-r bg-gray-100">
                                        +
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Kolom Stok -->
                            <div class="col-span-2 text-right">
                                @php
                                    $persenStok = $item->stock > 0 ? min(100, ($item->stock / 100) * 100) : 0;
                                    $warnaStok = $persenStok > 50 ? 'text-green-600' : ($persenStok > 20 ? 'text-yellow-600' : 'text-red-600');
                                @endphp
                                <span class="text-sm {{ $warnaStok }} font-medium">{{ $item->stock }}</span>
                            </div>
                            
                            <!-- Kolom Subtotal -->
                            <div class="col-span-2 text-right font-medium text-gray-900">
                                <span x-text="'Rp' + new Intl.NumberFormat('id-ID').format({{ $item->price }} * jumlah)"></span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        <p class="mt-2">Belum ada produk yang tersedia</p>
                        <a href="{{ url('/') }}"
                            class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Tambah Produk Pertama
                        </a>
                    </div>
                @endforelse
            </div>

            @if($produk->hasPages())
                <div class="px-6 py-3 bg-gray-50 border-t flex flex-col sm:flex-row justify-between items-center">
                    <div class="text-sm text-gray-500 mb-2 sm:mb-0">
                        Menampilkan {{ $produk->firstItem() }} - {{ $produk->lastItem() }} dari {{ $produk->total() }} produk
                    </div>
                    <div>
                        {{ $produk->links() }}
                    </div>
                </div>
            @endif
        </div>
    </form>

    <!-- Footer Jual Sekarang -->
    <div id="footer-jual" class="hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-300 shadow-xl p-4 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="text-lg font-semibold text-gray-800">
                Total: <span id="footer-total" class="text-blue-600">Rp0</span>
            </div>
            <button id="footer-jual-sekarang"
                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                Jual Sekarang
            </button>
        </div>
    </div>
</div>

<!-- Modal Alamat Pengiriman -->
<div id="shippingModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 hidden">
    <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg overflow-hidden animate-fadeIn">
        <form id="shipping-form" method="POST" action="{{ route('checkout.barang') }}">
            @csrf
            <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold">🧾 Alamat Pengiriman</h2>
                <button type="button" class="text-white text-xl" onclick="closeModal()">×</button>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Nama Penerima</label>
                    <input type="text" name="recipient_name" class="mt-1 w-full rounded-md border-gray-300" required>
                </div>
                <div>
                    <label class="text-sm font-medium">Nomor Telepon</label>
                    <input type="tel" name="phone_number" class="mt-1 w-full rounded-md border-gray-300" required>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium">Alamat Lengkap</label>
                    <textarea name="address" rows="2" class="mt-1 w-full rounded-md border-gray-300" required></textarea>
                </div>
                <div>
                    <label class="text-sm font-medium">Provinsi</label>
                    <select name="province" id="province" class="mt-1 w-full rounded-md border-gray-300" required></select>
                </div>
                <div>
                    <label class="text-sm font-medium">Kota/Kabupaten</label>
                    <select name="city" id="city" class="mt-1 w-full rounded-md border-gray-300" required disabled></select>
                </div>
                <div>
                    <label class="text-sm font-medium">Kecamatan</label>
                    <select name="subdistrict" id="subdistrict" class="mt-1 w-full rounded-md border-gray-300" required disabled></select>
                </div>
                <div>
                    <label class="text-sm font-medium">Kode Pos</label>
                    <input type="text" name="postal_code" class="mt-1 w-full rounded-md border-gray-300" required>
                </div>
                <div>
                    <label class="text-sm font-medium">Metode Pengiriman</label>
                    <select name="shipping_method" class="mt-1 w-full rounded-md border-gray-300" required>
                        <option value="">Pilih Metode</option>
                        <option value="jne">JNE</option>
                        <option value="tiki">TIKI</option>
                        <option value="pos">POS Indonesia</option>
                        <option value="same_day">Same Day Delivery</option>
                    </select>
                </div>
                <div class="md:col-span-2 text-right text-lg font-semibold text-blue-600 pt-4 border-t">
                    Total Pembayaran: <span id="modal-total-amount">Rp0</span>
                </div>
            </div>

            <div class="bg-gray-100 px-6 py-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Batal</button>
                <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Lanjutkan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Saat seluruh halaman telah dimuat
document.addEventListener('DOMContentLoaded', function () {

    // ===============================
    // Fungsi: Menampilkan dan Menyembunyikan Modal
    // ===============================
    function openModal() {
        document.getElementById('shippingModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('shippingModal').classList.add('hidden');
    }

    // ===============================
    // Fungsi: Load daftar provinsi dari JSON publik
    // ===============================
    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');
    const subdistrictSelect = document.getElementById('subdistrict');
    const form = document.getElementById('shipping-form');

    let selectedProvinceName = '';
    let selectedCityName = '';
    let selectedSubdistrictName = '';

    fetch('https://ihsaninh.github.io/wilayah-indonesia/provinces.json')
        .then(res => res.json())
        .then(data => {
            provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
            data.forEach(prov => {
                provinceSelect.innerHTML += `<option value="${prov.id}" data-name="${prov.value}">${prov.value}</option>`;
            });
            provinceSelect.disabled = false;
        });

    // ===============================
    // Fungsi: Load kota berdasarkan provinsi yang dipilih
    // ===============================
    provinceSelect.addEventListener('change', () => {
        const provinceId = provinceSelect.value;
        selectedProvinceName = provinceSelect.options[provinceSelect.selectedIndex].text;
        if (!provinceId) return;

        citySelect.disabled = true;
        citySelect.innerHTML = '<option value="">Memuat Kota...</option>';

        fetch(`https://ihsaninh.github.io/wilayah-indonesia/${provinceId}/regencies.json`)
            .then(res => res.json())
            .then(data => {
                citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                data.forEach(city => {
                    const fullName = `${city.type} ${city.value}`;
                    citySelect.innerHTML += `<option value="${city.id}" data-name="${fullName}">${fullName}</option>`;
                });
                citySelect.disabled = false;
                subdistrictSelect.disabled = true;
                subdistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            });
    });

    // ===============================
    // Fungsi: Load kecamatan berdasarkan kota yang dipilih
    // ===============================
    citySelect.addEventListener('change', () => {
        const provinceId = provinceSelect.value;
        const cityId = citySelect.value;
        selectedCityName = citySelect.options[citySelect.selectedIndex].text;
        if (!cityId) return;

        subdistrictSelect.disabled = true;
        subdistrictSelect.innerHTML = '<option value="">Memuat Kecamatan...</option>';

        fetch(`https://ihsaninh.github.io/wilayah-indonesia/${provinceId}/${cityId}/district.json`)
            .then(res => res.json())
            .then(data => {
                subdistrictSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                data.forEach(dist => {
                    subdistrictSelect.innerHTML += `<option value="${dist.id}" data-name="${dist.value}">${dist.value}</option>`;
                });
                subdistrictSelect.disabled = false;
            });
    });

    // ===============================
    // Fungsi: Saat Submit Form → Ganti Value Provinsi/Kota/Kecamatan Jadi Nama (bukan ID)
    // ===============================
    form.addEventListener('submit', function (e) {
        // Simpan nama kecamatan juga
        selectedSubdistrictName = subdistrictSelect.options[subdistrictSelect.selectedIndex].text;

        // Ganti value select jadi nama wilayah
        provinceSelect.name = 'province';
        citySelect.name = 'city';
        subdistrictSelect.name = 'subdistrict';

        provinceSelect.insertAdjacentHTML('afterend', `<input type="hidden" name="province" value="${selectedProvinceName}">`);
        citySelect.insertAdjacentHTML('afterend', `<input type="hidden" name="city" value="${selectedCityName}">`);
        subdistrictSelect.insertAdjacentHTML('afterend', `<input type="hidden" name="subdistrict" value="${selectedSubdistrictName}">`);

        // Hapus value id agar tidak terkirim
        provinceSelect.disabled = true;
        citySelect.disabled = true;
        subdistrictSelect.disabled = true;
    });

    // ===============================
    // Fungsi: Select All Checkbox Produk
    // ===============================
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.product-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                checkbox.dispatchEvent(new Event('change'));
            });
        });
    }

    // ===============================
    // Fungsi: Konfirmasi saat klik tombol hapus-terpilih
    // ===============================
    const hapusBtn = document.getElementById('hapus-terpilih');
    if (hapusBtn) {
        hapusBtn.addEventListener('click', function (e) {
            if (!confirm('Yakin ingin menghapus produk yang dipilih?')) {
                e.preventDefault();
            }
        });
    }

    // ===============================
    // Fungsi: Update Total Harga di Footer
    // ===============================
    window.updateTotalHarga = function () {
        let total = 0;
        const checkedItems = document.querySelectorAll('.product-checkbox:checked');

        checkedItems.forEach(checkbox => {
            const item = checkbox.closest('.produk-item');
            const harga = parseFloat(item.querySelector('.harga-produk').dataset.harga);
            const jumlah = parseInt(item.querySelector('input[type="number"]').value);
            total += harga * jumlah;
        });

        const footerTotal = document.getElementById('footer-total');
        const footerJual = document.getElementById('footer-jual');

        if (checkedItems.length > 0) {
            footerTotal.textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(total);
            footerJual.classList.remove('hidden');
        } else {
            footerJual.classList.add('hidden');
        }
    };

    // ===============================
    // Fungsi: Saat Klik "Jual Sekarang" → tampilkan modal + isi total + hidden input
    // ===============================
    document.getElementById('footer-jual-sekarang').addEventListener('click', function () {
        const checkedItems = document.querySelectorAll('.product-checkbox:checked');
        if (checkedItems.length === 0) {
            alert('Silakan pilih minimal 1 produk untuk dijual');
            return;
        }

        const productData = [];
        let totalAmount = 0;

        checkedItems.forEach(checkbox => {
            const item = checkbox.closest('.produk-item');
            const productId = item.querySelector('input[name="selected[]"]').value;
            const productName = item.querySelector('.font-medium.text-gray-900').textContent;
            const price = parseFloat(item.querySelector('.harga-produk').dataset.harga);
            const quantity = parseInt(item.querySelector('input[type="number"]').value);
            const subtotal = price * quantity;

            productData.push({
                product_id: productId,
                name: productName,
                price: price,
                quantity: quantity,
                subtotal: subtotal
            });

            totalAmount += subtotal;
        });

        // Tampilkan total di modal
        document.getElementById('modal-total-amount').textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(totalAmount);

        // Tambahkan data produk sebagai input hidden
        document.querySelectorAll('input[name^="products"]').forEach(el => el.remove());

        productData.forEach((product, index) => {
            for (const key in product) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `products[${index}][${key}]`;
                input.value = product[key];
                form.appendChild(input);
            }
        });

        // Tampilkan modal
        openModal();
    });
});
</script>
@endsection