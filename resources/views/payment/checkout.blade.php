@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900">Pilih Paket Berlangganan</h1>
            <p class="mt-4 text-lg text-gray-600">Pilih paket yang sesuai dengan kebutuhan bisnis dropship Anda</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($plans as $key => $plan)
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <div class="px-6 py-8 bg-white">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $plan['name'] }}</h2>
                        <div class="mt-4">
                            <span class="text-4xl font-extrabold text-gray-900">Rp {{ number_format($plan['price'], 0, ',', '.') }}</span>
                            <span class="text-gray-600">/bulan</span>
                        </div>
                    </div>
                </div>
                <div class="px-6 pt-6 pb-8 bg-gray-50">
                    <ul class="space-y-4">
                        @foreach($plan['features'] as $feature)
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="ml-2 text-gray-700">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <div class="mt-8">
                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan" value="{{ $key }}">
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Pilih Paket
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection