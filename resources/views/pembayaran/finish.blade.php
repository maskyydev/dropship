@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-green-100 p-6 border-b border-green-200">
            <div class="flex items-center">
                <svg class="h-10 w-10 text-green-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <div>
                    <h1 class="text-2xl font-bold text-green-800">Pembayaran Berhasil</h1>
                    <p class="text-green-600">Terima kasih telah melakukan pembayaran</p>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h2 class="text-lg font-semibold mb-4">Detail Transaksi</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order ID:</span>
                            <span class="font-medium">{{ $payment->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal:</span>
                            <span class="font-medium">{{ $payment->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Metode Pembayaran:</span>
                            <span class="font-medium">{{ ucfirst($payment->payment_method) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-bold text-green-600">{{ ucfirst($payment->status) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t pt-3">
                            <span class="text-gray-700">Total Pembayaran:</span>
                            <span class="text-blue-600">Rp{{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-lg font-semibold mb-4">Detail Pengiriman</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-gray-600">Nama Penerima:</p>
                            <p class="font-medium">{{ $payment->shipping->recipient_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Alamat:</p>
                            <p class="font-medium">{{ $payment->shipping->address }}</p>
                            <p class="font-medium">{{ $payment->shipping->city }}, {{ $payment->shipping->province }} {{ $payment->shipping->postal_code }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Nomor Telepon:</p>
                            <p class="font-medium">{{ $payment->shipping->phone_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Metode Pengiriman:</p>
                            <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $payment->shipping->shipping_method)) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Status Pengiriman:</p>
                            <p class="font-medium">{{ ucfirst($payment->shipping->status) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8">
                <a href="{{ route('home') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection