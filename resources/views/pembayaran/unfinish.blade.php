@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-yellow-100 p-6 border-b border-yellow-200">
            <div class="flex items-center">
                <svg class="h-10 w-10 text-yellow-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8z" />
                </svg>
                <div>
                    <h1 class="text-2xl font-bold text-yellow-800">Pembayaran Tertunda</h1>
                    <p class="text-yellow-600">Pembayaran Anda masih belum selesai. Silakan selesaikan pembayaran untuk memproses pesanan.</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <p class="text-gray-700 mb-4">
                Jika Anda sudah melakukan pembayaran, mohon tunggu beberapa saat sampai sistem memperbarui status transaksi Anda.
            </p>

            <div class="mt-6">
                <a href="{{ route('home') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
