<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        // Filter parameters
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => $request->input('status'),
            'payment_method' => $request->input('payment_method')
        ];

        // Get sales with filters
        $sales = Sale::with(['user', 'items.product'])
                    ->filter($filters)
                    ->latest()
                    ->paginate(10);

        // Statistics
        $todaySales = Sale::whereDate('sale_date', today())->sum('grand_total');
        $monthlySales = Sale::whereMonth('sale_date', now()->month)->sum('grand_total');
        $totalSales = Sale::sum('grand_total');

        // Best selling products
        $bestSellers = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
                            ->with('product')
                            ->groupBy('product_id')
                            ->orderByDesc('total_sold')
                            ->take(5)
                            ->get();

        return view('admin.sales.index', compact(
            'sales',
            'todaySales',
            'monthlySales',
            'totalSales',
            'bestSellers',
            'filters'
        ));
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('admin.sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,credit_card,bank_transfer,e_wallet',
            'notes' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($validated) {
            // Calculate totals
            $totalAmount = 0;
            $totalDiscount = 0;
            $totalTax = 0;

            foreach ($validated['items'] as $item) {
                $subtotal = ($item['unit_price'] * $item['quantity']) - $item['discount'] + $item['tax'];
                $totalAmount += $item['unit_price'] * $item['quantity'];
                $totalDiscount += $item['discount'];
                $totalTax += $item['tax'];
            }

            $grandTotal = $totalAmount - $totalDiscount + $totalTax;

            // Create sale
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'invoice_number' => 'INV-' . Str::upper(Str::random(8)) . '-' . time(),
                'sale_date' => $validated['sale_date'],
                'total_amount' => $totalAmount,
                'total_discount' => $totalDiscount,
                'total_tax' => $totalTax,
                'grand_total' => $grandTotal,
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null
            ]);

            // Create sale items and update product stock
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'],
                    'tax' => $item['tax'],
                    'subtotal' => ($item['unit_price'] * $item['quantity']) - $item['discount'] + $item['tax']
                ]);

                // Update product stock
                $product->decrement('stock', $item['quantity']);
            }

            return redirect()->route('admin.sales.index')
                            ->with('success', 'Penjualan berhasil dicatat');
        });
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product']);
        return view('admin.sales.show', compact('sale'));
    }

    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            // Restore product stock
            foreach ($sale->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            // Delete sale items
            $sale->items()->delete();

            // Delete sale
            $sale->delete();
        });

        return redirect()->route('admin.sales.index')
                        ->with('success', 'Penjualan berhasil dihapus');
    }
}