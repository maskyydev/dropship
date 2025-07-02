@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Total Produk</p>
                    <h3 class="text-2xl font-bold">{{ $totalProducts }}</h3>
                </div>
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Total Penjualan</p>
                    <h3 class="text-2xl font-bold">{{ $totalSales }}</h3>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Total Pendapatan</p>
                    <h3 class="text-2xl font-bold">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Total Pengguna</p>
                    <h3 class="text-2xl font-bold">{{ $totalUsers }}</h3>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Weekly Sales Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Penjualan Mingguan</h3>
            <canvas id="weeklySalesChart" height="250"></canvas>
        </div>
        
        <!-- Monthly Revenue Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Pendapatan Bulanan</h3>
            <canvas id="monthlyRevenueChart" height="250"></canvas>
        </div>
    </div>
    
    <!-- Top Products -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Produk Terlaris</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terjual</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($topProducts as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img src="{{ $product->images->first() ? asset('storage/products/' . $product->images->first()->filename) : asset('images/default-product.png') }}"
                                        alt="{{ $product->name }}"
                                        class="h-10 w-10 rounded-full object-cover">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                {{ $product->category ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Rp{{ number_format($product->price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->stock }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->sales_count }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Weekly Sales Chart
    const weeklyCtx = document.getElementById('weeklySalesChart').getContext('2d');
    const weeklySalesChart = new Chart(weeklyCtx, {
        type: 'bar',
        data: {
            labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            datasets: [{
                label: 'Penjualan',
                data: [
                    {{ $weeklySales->where('sale_date', '>=', Carbon\Carbon::now()->startOfWeek()->addDays(0))->count() }},
                    {{ $weeklySales->where('sale_date', '>=', Carbon\Carbon::now()->startOfWeek()->addDays(1))->count() }},
                    {{ $weeklySales->where('sale_date', '>=', Carbon\Carbon::now()->startOfWeek()->addDays(2))->count() }},
                    {{ $weeklySales->where('sale_date', '>=', Carbon\Carbon::now()->startOfWeek()->addDays(3))->count() }},
                    {{ $weeklySales->where('sale_date', '>=', Carbon\Carbon::now()->startOfWeek()->addDays(4))->count() }},
                    {{ $weeklySales->where('sale_date', '>=', Carbon\Carbon::now()->startOfWeek()->addDays(5))->count() }},
                    {{ $weeklySales->where('sale_date', '>=', Carbon\Carbon::now()->startOfWeek()->addDays(6))->count() }}
                ],
                backgroundColor: 'rgba(79, 70, 229, 0.7)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Monthly Revenue Chart
    const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    const monthlyRevenueChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: [
                    {{ $monthlySales->whereBetween('sale_date', [Carbon\Carbon::now()->startOfMonth(), Carbon\Carbon::now()->startOfMonth()->addDays(7)])->sum('total_price') }},
                    {{ $monthlySales->whereBetween('sale_date', [Carbon\Carbon::now()->startOfMonth()->addDays(8), Carbon\Carbon::now()->startOfMonth()->addDays(14)])->sum('total_price') }},
                    {{ $monthlySales->whereBetween('sale_date', [Carbon\Carbon::now()->startOfMonth()->addDays(15), Carbon\Carbon::now()->startOfMonth()->addDays(21)])->sum('total_price') }},
                    {{ $monthlySales->whereBetween('sale_date', [Carbon\Carbon::now()->startOfMonth()->addDays(22), Carbon\Carbon::now()->endOfMonth()])->sum('total_price') }}
                ],
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
@endsection