@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 text-center">
            <div class="text-green-500 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Berhasil!</h2>
            <p class="text-gray-600 mb-6">Terima kasih telah berbelanja dengan kami.</p>
            
            <div class="bg-gray-50 p-4 rounded-lg mb-6 text-left">
                <h3 class="font-semibold text-gray-700 mb-2">Detail Pesanan</h3>
                <div class="flex justify-between mb-1">
                    <span class="text-gray-600">Nomor Invoice</span>
                    <span class="font-medium">{{ $sale->invoice_number }}</span>
                </div>
                <div class="flex justify-between mb-1">
                    <span class="text-gray-600">Tanggal</span>
                    <span class="font-medium">{{ $sale->sale_date->format('d M Y H:i') }}</span>
                </div>
                <div class="flex justify-between mb-1">
                    <span class="text-gray-600">Metode Pembayaran</span>
                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</span>
                </div>
                <div class="flex justify-between mt-3 pt-3 border-t">
                    <span class="text-gray-600 font-semibold">Total</span>
                    <span class="font-bold text-lg">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <a href="{{ route('home') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors font-medium">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection