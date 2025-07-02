@extends('layouts.admin')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-6">Tambah Penjualan Baru</h2>

    <form id="saleForm" action="{{ route('admin.sales.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Sale Info -->
            <div class="md:col-span-1">
                <div class="space-y-4">
                    <div>
                        <label for="sale_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Penjualan *</label>
                        <input type="datetime-local" name="sale_date" id="sale_date" 
                               value="{{ old('sale_date', now()->format('Y-m-d\TH:i')) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('sale_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran *</label>
                        <select name="payment_method" id="payment_method" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach(App\Models\Sale::$paymentMethods as $key => $method)
                                <option value="{{ $key }}" {{ old('payment_method') == $key ? 'selected' : '' }}>{{ $method }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="notes" id="notes" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Product Selection -->
            <div class="md:col-span-2">
                <div class="mb-4">
                    <label for="product_search" class="block text-sm font-medium text-gray-700 mb-1">Cari Produk</label>
                    <div class="flex">
                        <input type="text" id="product_search" placeholder="Ketik nama atau barcode produk..."
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="button" id="search_product" 
                                class="bg-indigo-600 text-white px-4 py-2 rounded-r-lg hover:bg-indigo-700 transition-colors">
                            Cari
                        </button>
                    </div>
                </div>

                <!-- Selected Products -->
                <div class="border rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="selected_products" class="bg-white divide-y divide-gray-200">
                            <!-- Items will be added here dynamically -->
                            <tr id="no_products" class="text-center text-gray-500">
                                <td colspan="5" class="px-4 py-4">Belum ada produk ditambahkan</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right font-medium">Total</td>
                                <td id="grand_total" class="px-4 py-3 font-medium">Rp0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('admin.sales.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                Batal
            </a>
            <button type="submit" id="submit_btn" disabled
                    class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors opacity-50 cursor-not-allowed">
                Simpan Penjualan
            </button>
        </div>
    </form>
</div>

<!-- Product Selection Modal -->
<div id="product_modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Pilih Produk</h3>
            <button id="close_modal" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <div class="mb-4">
            <input type="text" id="modal_product_search" placeholder="Cari produk..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        
        <div id="product_list" class="max-h-96 overflow-y-auto">
            <!-- Products will be loaded here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Product selection functionality
        const products = @json($products);
        let selectedProducts = [];
        
        // Open modal when search button clicked
        document.getElementById('search_product').addEventListener('click', function() {
            document.getElementById('product_modal').classList.remove('hidden');
            renderProductList(products);
        });
        
        // Close modal
        document.getElementById('close_modal').addEventListener('click', function() {
            document.getElementById('product_modal').classList.add('hidden');
        });
        
        // Filter products in modal
        document.getElementById('modal_product_search').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const filteredProducts = products.filter(product => 
                product.name.toLowerCase().includes(searchTerm) || 
                (product.barcode && product.barcode.toLowerCase().includes(searchTerm))
            renderProductList(filteredProducts);
        });
        
        // Render product list in modal
        function renderProductList(productsToRender) {
            const productList = document.getElementById('product_list');
            productList.innerHTML = '';
            
            if (productsToRender.length === 0) {
                productList.innerHTML = '<div class="text-center text-gray-500 py-4">Tidak ada produk ditemukan</div>';
                return;
            }
            
            productsToRender.forEach(product => {
                const isSelected = selectedProducts.some(p => p.id === product.id);
                const productCard = document.createElement('div');
                productCard.className = `border rounded-lg p-3 mb-2 cursor-pointer hover:bg-gray-50 ${isSelected ? 'bg-indigo-50 border-indigo-200' : ''}`;
                productCard.innerHTML = `
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="h-10 w-10 mr-3">
                                <img src="${product.image ? '/storage/' + product.image : '/images/default-product.png'}" 
                                     alt="${product.name}" class="h-full w-full object-cover rounded-lg">
                            </div>
                            <div>
                                <h4 class="font-medium">${product.name}</h4>
                                <p class="text-sm text-gray-500">Stok: ${product.stock}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">Rp${product.price.toLocaleString('id-ID')}</p>
                            ${isSelected ? 
                                '<p class="text-xs text-green-600">Sudah ditambahkan</p>' : 
                                '<button class="add_product mt-1 bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700" data-id="${product.id}">Tambah</button>'}
                        </div>
                    </div>
                `;
                productList.appendChild(productCard);
            });
            
            // Add event listeners to add buttons
            document.querySelectorAll('.add_product').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = parseInt(this.getAttribute('data-id'));
                    const product = products.find(p => p.id === productId);
                    addProductToSale(product);
                });
            });
        }
        
        // Add product to sale
        function addProductToSale(product) {
            // Check if product already added
            const existingProduct = selectedProducts.find(p => p.id === product.id);
            if (existingProduct) {
                alert('Produk ini sudah ditambahkan');
                return;
            }
            
            // Add to selected products
            selectedProducts.push({
                id: product.id,
                name: product.name,
                price: product.price,
                stock: product.stock,
                image: product.image,
                quantity: 1,
                discount: 0,
                tax: 0,
                subtotal: product.price
            });
            
            // Update form inputs and UI
            updateSaleForm();
            renderSelectedProducts();
            
            // Close modal
            document.getElementById('product_modal').classList.add('hidden');
        }
        
        // Render selected products table
        function renderSelectedProducts() {
            const tbody = document.getElementById('selected_products');
            
            if (selectedProducts.length === 0) {
                tbody.innerHTML = '<tr id="no_products" class="text-center text-gray-500"><td colspan="5" class="px-4 py-4">Belum ada produk ditambahkan</td></tr>';
                return;
            }
            
            // Remove "no products" row if exists
            const noProductsRow = document.getElementById('no_products');
            if (noProductsRow) noProductsRow.remove();
            
            tbody.innerHTML = '';
            
            selectedProducts.forEach((product, index) => {
                const row = document.createElement('tr');
                row.className = 'product_row';
                row.dataset.id = product.id;
                row.innerHTML = `
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 mr-3">
                                <img src="${product.image ? '/storage/' + product.image : '/images/default-product.png'}" 
                                     alt="${product.name}" class="h-full w-full object-cover rounded-lg">
                            </div>
                            <div>
                                <h4 class="font-medium">${product.name}</h4>
                                <p class="text-xs text-gray-500">Stok: ${product.stock}</p>
                            </div>
                        </div>
                        <input type="hidden" name="items[${index}][product_id]" value="${product.id}">
                    </td>
                    <td class="px-4 py-4">
                        <input type="number" name="items[${index}][unit_price]" value="${product.price}" min="0" step="100"
                               class="w-24 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 price_input">
                    </td>
                    <td class="px-4 py-4">
                        <input type="number" name="items[${index}][quantity]" value="${product.quantity}" min="1" max="${product.stock}"
                               class="w-20 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500 quantity_input">
                    </td>
                    <td class="px-4 py-4 subtotal_cell">
                        Rp${product.subtotal.toLocaleString('id-ID')}
                    </td>
                    <td class="px-4 py-4">
                        <button type="button" class="remove_product text-red-600 hover:text-red-900 text-sm">
                            Hapus
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
            
            // Add event listeners to quantity and price inputs
            document.querySelectorAll('.quantity_input, .price_input').forEach(input => {
                input.addEventListener('change', function() {
                    updateProductQuantityOrPrice(this);
                });
            });
            
            // Add event listeners to remove buttons
            document.querySelectorAll('.remove_product').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('.product_row');
                    const productId = parseInt(row.dataset.id);
                    removeProductFromSale(productId);
                });
            });
        }
        
        // Update product quantity or price
        function updateProductQuantityOrPrice(input) {
            const row = input.closest('.product_row');
            const productId = parseInt(row.dataset.id);
            const product = selectedProducts.find(p => p.id === productId);
            
            if (input.classList.contains('quantity_input')) {
                const newQuantity = parseInt(input.value);
                if (newQuantity > product.stock) {
                    alert('Stok tidak mencukupi');
                    input.value = product.quantity;
                    return;
                }
                product.quantity = newQuantity;
            } else if (input.classList.contains('price_input')) {
                product.price = parseFloat(input.value);
            }
            
            // Recalculate subtotal
            product.subtotal = product.price * product.quantity;
            
            // Update UI
            row.querySelector('.subtotal_cell').textContent = `Rp${product.subtotal.toLocaleString('id-ID')}`;
            updateSaleForm();
        }
        
        // Remove product from sale
        function removeProductFromSale(productId) {
            selectedProducts = selectedProducts.filter(p => p.id !== productId);
            updateSaleForm();
            renderSelectedProducts();
        }
        
        // Update form inputs and totals
        function updateSaleForm() {
            // Calculate totals
            const grandTotal = selectedProducts.reduce((sum, product) => sum + product.subtotal, 0);
            
            // Update UI
            document.getElementById('grand_total').textContent = `Rp${grandTotal.toLocaleString('id-ID')}`;
            
            // Enable/disable submit button
            const submitBtn = document.getElementById('submit_btn');
            if (selectedProducts.length > 0) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
    });
</script>
@endpush