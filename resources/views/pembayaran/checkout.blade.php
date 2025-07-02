@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Checkout Pembayaran</h1>

        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold mb-4">Detail Pengiriman</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Data pengiriman -->
                    <div>
                        <label class="text-sm text-gray-600">Nama Penerima:</label>
                        <p class="font-medium">{{ $shipping->recipient_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Nomor Telepon:</label>
                        <p class="font-medium">{{ $shipping->phone_number }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm text-gray-600">Alamat Lengkap:</label>
                        <p class="font-medium">{{ $shipping->address }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Provinsi:</label>
                        <p class="font-medium">{{ $shipping->province ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Kota:</label>
                        <p class="font-medium">{{ $shipping->city ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Kecamatan:</label>
                        <p class="font-medium">{{ $shipping->subdistrict ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Kode Pos:</label>
                        <p class="font-medium">{{ $shipping->postal_code }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Metode Pengiriman:</label>
                        <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $shipping->shipping_method)) }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Biaya Pengiriman:</label>
                        <p class="font-medium">Rp{{ number_format($shipping->shipping_cost, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Ringkasan Pembayaran</h2>
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">Rp{{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Biaya Pengiriman:</span>
                        <span class="font-medium">Rp{{ number_format($shipping->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-3">
                        <span class="text-gray-700">Total Pembayaran:</span>
                        <span class="text-blue-600">Rp{{ number_format($payment->amount + $shipping->shipping_cost, 0, ',', '.') }}</span>
                    </div>

                    <div class="mb-4">
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Pilih Metode Pembayaran:</label>
                        <select id="payment_method" class="w-full border rounded px-3 py-2">
                            <option value="">Pilih metode</option>
                            <option value="gopay">GoPay</option>
                            <option value="shopeepay">ShopeePay</option>
                            <option value="qris">QRIS</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="bca_va">BCA Virtual Account</option>
                            <option value="bni_va">BNI Virtual Account</option>
                            <option value="bri_va">BRI Virtual Account</option>
                            <option value="cstore">Alfamart / Indomaret</option>
                        </select>
                    </div>
                </div>

                {{-- Simpan CSRF token di input hidden --}}
                <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

                <button id="pay-button" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700">
                    Bayar Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('pay-button').onclick = function () {
    const selectedMethod = document.getElementById('payment_method').value;
    const csrfToken = document.getElementById('csrf_token').value;

    if (!selectedMethod) {
        alert('Silakan pilih metode pembayaran terlebih dahulu.');
        return;
    }

    fetch("{{ route('pembayaran.getRedirectUrl') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
            payment_id: {{ $payment->id }},
            method: selectedMethod
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.redirect_url) {
            window.location.href = data.redirect_url;
        } else {
            alert('Gagal memproses redirect Midtrans.');
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
};
</script>
@endsection
