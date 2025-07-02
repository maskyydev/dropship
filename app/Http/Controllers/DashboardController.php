<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Tambahkan ini di atas

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalSales = Sale::count();
        $totalRevenue = Sale::sum('grand_total'); // Gunakan grand_total dari tabel sales
        $totalUsers = User::count();

        // Penjualan Mingguan
        $weeklySales = Sale::whereBetween('sale_date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->get();

        // Penjualan Bulanan
        $monthlySales = Sale::whereMonth('sale_date', Carbon::now()->month)
                            ->whereYear('sale_date', Carbon::now()->year)
                            ->get();

        // Produk Terlaris berdasarkan jumlah quantity terbanyak dalam tabel sale_items
        $topProducts = Product::with('image')
            ->select('products.id', 'products.name', 'products.price', 'products.stock', 'products.category', DB::raw('SUM(sale_items.quantity) as sales_count'))
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->groupBy('products.id', 'products.name', 'products.price', 'products.stock', 'products.category')
            ->orderByDesc('sales_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalSales',
            'totalRevenue',
            'totalUsers',
            'weeklySales',
            'monthlySales',
            'topProducts'
        ));
    }
}
