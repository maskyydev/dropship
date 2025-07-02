@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Checkout</h2>
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Produk Anda</h3>
                <div class="flex items-center border-b pb-4 mb-4">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded">
                    <div class="ml-4">
                        <h4 class="font-medium text-gray-800">{{ $product->name }}</h4>
                        <p class="text-gray-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                    <div class="ml-auto">
                        <div class="flex items-center">
                            <button id="decrement" class="px-2 py-1 border rounded-l">-</button>
                            <input type="number" id="quantity" value="1" min="1" class="w-12 text-center border-t border-b">
                            <button id="increment" class="px-2 py-1 border rounded-r">+</button>
                        </div>
                    </div>
                </div>
                
                <div class="border-b pb-4 mb-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Subtotal</span>
                        <span id="subtotal" class="font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Pajak</span>
                        <span id="tax" class="font-medium">Rp 0</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Diskon</span>
                        <span id="discount" class="font-medium">Rp 0</span>
                    </div>
                </div>
                
                <div class="flex justify-between text-lg font-bold mb-6">
                    <span>Total</span>
                    <span id="total">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <form id="payment-form" class="flex-1">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" id="hiddenQuantity" value="1">
                <button type="submit" id="pay-button" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition-colors font-medium flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Bayar Sekarang
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Tambahkan script Midtrans -->
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
<script>
    // Update quantity
    document.getElementById('increment').addEventListener('click', function() {
        const quantityInput = document.getElementById('quantity');
        let quantity = parseInt(quantityInput.value);
        quantityInput.value = quantity + 1;
        updateTotals(quantity + 1);
    });

    document.getElementById('decrement').addEventListener('click', function() {
        const quantityInput = document.getElementById('quantity');
        let quantity = parseInt(quantityInput.value);
        if (quantity > 1) {
            quantityInput.value = quantity - 1;
            updateTotals(quantity - 1);
        }
    });

    document.getElementById('quantity').addEventListener('change', function() {
        let quantity = parseInt(this.value);
        if (quantity < 1) {
            quantity = 1;
            this.value = 1;
        }
        updateTotals(quantity);
    });

    function updateTotals(quantity) {
        const price = {{ $product->price }};
        const subtotal = price * quantity;
        
        document.getElementById('subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('total').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('hiddenQuantity').value = quantity;
    }

    // Handle pembayaran
    document.getElementById('pay-button').onclick = function(e){
        e.preventDefault();
        
        // Tampilkan loading
        const button = this;
        button.disabled = true;
        button.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';
        
        // Kirim data ke backend untuk mendapatkan snap token
        fetch("{{ route('checkout.process') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                product_id: document.querySelector('input[name="product_id"]').value,
                quantity: document.getElementById('hiddenQuantity').value
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Buka popup pembayaran Midtrans
            window.snap.pay(data.snap_token, {
                onSuccess: function(result){
                    window.location.href = "{{ route('checkout.success') }}?sale_id=" + data.sale_id;
                },
                onPending: function(result){
                    window.location.href = "{{ route('checkout.success') }}?sale_id=" + data.sale_id;
                },
                onError: function(result){
                    alert('Pembayaran gagal: ' + result.status_message);
                    button.disabled = false;
                    button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg> Bayar Sekarang';
                },
                onClose: function(){
                    button.disabled = false;
                    button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg> Bayar Sekarang';
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan: ' + error.message);
            button.disabled = false;
            button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg> Bayar Sekarang';
        });
    };
</script>
@endsection