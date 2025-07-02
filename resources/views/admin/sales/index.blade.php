@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Penjualan Hari Ini</p>
                    <h3 class="text-2xl font-bold">Rp{{ number_format($todaySales, 0, ',', '.') }}</h3>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Penjualan Bulan Ini</p>
                    <h3 class="text-2xl font-bold">Rp{{ number_format($monthlySales, 0, ',', '.') }}</h3>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Total Penjualan</p>
                    <h3 class="text-2xl font-bold">Rp{{ number_format($totalSales, 0, ',', '.') }}</h3>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Sales Table -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h2 class="text-xl font-semibold">Daftar Penjualan</h2>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.sales.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                    Tambah Penjualan
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.sales.index') }}" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $filters['start_date'] }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $filters['end_date'] }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        @foreach(App\Models\Sale::$statuses as $key => $status)
                            <option value="{{ $key }}" {{ $filters['status'] == $key ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                    <select name="payment_method" id="payment_method"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Metode</option>
                        @foreach(App\Models\Sale::$paymentMethods as $key => $method)
                            <option value="{{ $key }}" {{ $filters['payment_method'] == $key ? 'selected' : '' }}>{{ $method }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                    Filter
                </button>
                <a href="{{ route('admin.sales.index') }}" class="ml-2 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                    Reset
                </a>
            </div>
        </form>

        <!-- Sales Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sales as $sale)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-indigo-600">{{ $sale->invoice_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $sale->sale_date->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $sale->user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            Rp{{ number_format($sale->grand_total, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ App\Models\Sale::$paymentMethods[$sale->payment_method] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $sale->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($sale->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ App\Models\Sale::$statuses[$sale->status] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                            <form action="{{ route('admin.sales.destroy', $sale) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus penjualan ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data penjualan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    </div>

    <!-- Best Sellers -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Produk Terlaris</h2>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @forelse($bestSellers as $item)
            <div class="border rounded-lg p-4 text-center">
                <div class="h-16 w-16 mx-auto mb-2">
                    <img src="{{ $item->product->image ? asset('storage/'.$item->product->image) : asset('images/default-product.png') }}" 
                         alt="{{ $item->product->name }}" class="h-full w-full object-cover rounded-lg">
                </div>
                <h3 class="text-sm font-medium">{{ $item->product->name }}</h3>
                <p class="text-xs text-gray-500">Terjual: {{ $item->total_sold }}</p>
            </div>
            @empty
            <div class="md:col-span-5 text-center text-gray-500 py-4">
                Tidak ada data produk terlaris
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection