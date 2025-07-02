<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua kategori unik dari tabel products
        $categories = Product::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        // Query dasar
        $query = Product::query()->where('stock', '>', 0);

        // Filter pencarian
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter kategori
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Sorting
        switch ($request->sort) {
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString(); // tetap bawa query saat paging

        return view('home', [
            'products' => $products,
            'search' => $request->search,
            'selectedCategory' => $request->category ?? 'all',
            'selectedSort' => $request->sort ?? 'latest',
            'categories' => $categories,
        ]);
    }

    public function showProduct(Product $product)
    {
        $product->load(['marketing_files', 'preview']);

        $relatedProducts = Product::where('category', $product->category)
                            ->where('id', '!=', $product->id)
                            ->inRandomOrder()
                            ->limit(4)
                            ->get();

        return view('product-detail', compact('product', 'relatedProducts'));
    }
}