<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Daftar metode pembayaran ENUM
     */
    public static $paymentMethods = [
        'gopay' => 'GoPay',
        'shopeepay' => 'ShopeePay',
        'bank_transfer' => 'Bank Transfer',
        'echannel' => 'Mandiri VA (e-Channel)',
        'credit_card' => 'Credit Card',
        'qris' => 'QRIS',
        'cstore' => 'Convenience Store (Alfamart/Indomaret)',
        'akulaku' => 'Akulaku',
        'danamon_online' => 'Danamon Online',
        'bca_klikpay' => 'BCA KlikPay',
        'bni_va' => 'BNI Virtual Account',
        'permata_va' => 'Permata Virtual Account',
        'bca_va' => 'BCA Virtual Account',
        'bri_va' => 'BRI Virtual Account',
    ];

    public function __construct()
    {
        // Ambil konfigurasi Midtrans dari config/services.php
        Config::$serverKey     = config('services.midtrans.serverKey');
        Config::$isProduction  = config('services.midtrans.isProduction', false);
        Config::$isSanitized   = config('services.midtrans.isSanitized', true);
        Config::$is3ds         = config('services.midtrans.is3ds', true);

        // Logging untuk debug
        \Log::info('Midtrans Config Loaded', [
            'serverKey' => Config::$serverKey,
            'isProduction' => Config::$isProduction
        ]);
    }

    public function process(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'payment_method' => 'required|string|in:' . implode(',', array_keys(self::$paymentMethods)),
    ]);

    try {
        $user = auth()->user();
        $product = Product::findOrFail($validated['product_id']);
        $quantity = $validated['quantity'];
        $paymentMethod = $validated['payment_method']; // ambil dari request

        $subtotal = $product->price * $quantity;
        $grandTotal = $subtotal;

        // Generate invoice number unik
        $invoiceNumber = 'INV-' . uniqid();

        // Simpan transaksi utama dengan payment_method sesuai pilihan konsumen
        $sale = Sale::create([
            'user_id' => $user->id,
            'invoice_number' => $invoiceNumber,
            'sale_date' => now(),
            'total_amount' => $subtotal,
            'grand_total' => $grandTotal,
            'payment_method' => $paymentMethod,
            'status' => 'pending',
        ]);

        // Simpan detail item
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'subtotal' => $subtotal,
        ]);

        // Jika mau membatasi hanya metode pembayaran yang dipilih, enabled_payments bisa di-set cuma satu
        $enabledPayments = [$paymentMethod];

        $params = [
            'transaction_details' => [
                'order_id' => $invoiceNumber,
                'gross_amount' => $grandTotal,
            ],
            'item_details' => [[
                'id' => $product->id,
                'price' => $product->price,
                'quantity' => $quantity,
                'name' => $product->name,
            ]],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'enabled_payments' => $enabledPayments,
        ];

        Log::info('Midtrans Request Params:', $params);

        // Ambil Snap token
        $snapToken = Snap::getSnapToken($params);

        if (!$snapToken) {
            Log::error('Snap token gagal dibuat dari Midtrans.');
            throw new \Exception("Snap token tidak ditemukan dari Midtrans.");
        }

        Log::info('Snap Token:', ['token' => $snapToken]);

        return response()->json([
            'snap_token' => $snapToken,
            'sale_id' => $sale->id,
        ]);

    } catch (\Exception $e) {
        Log::error('Midtrans Error:', ['message' => $e->getMessage()]);

        return response()->json([
            'error' => true,
            'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
        ], 500);
    }
}


    public function success(Request $request)
    {
        $sale = Sale::findOrFail($request->sale_id);

        // Default ke salah satu payment method yang valid, misal 'qris'
        $paymentMethod = $request->payment_method;

        // Jika tidak ada atau tidak valid, pakai 'qris' (atau sesuaikan dengan enum kamu)
        $validMethods = array_keys(self::$paymentMethods);

        if (!$paymentMethod || !in_array($paymentMethod, $validMethods)) {
            $paymentMethod = 'qris'; // contoh default yang valid
        }

        $sale->update([
            'status' => 'completed',
            'payment_method' => $paymentMethod,
        ]);

        return view('success', compact('sale'));
    }


    public function notification()
    {
        try {
            $notif = new Notification();

            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $orderId = $notif->order_id;
            $fraud = $notif->fraud_status;

            $sale = Sale::where('invoice_number', $orderId)->first();

            if (!$sale) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order tidak ditemukan'
                ]);
            }

            switch ($transaction) {
                case 'capture':
                    $sale->status = ($type === 'credit_card' && $fraud === 'challenge') ? 'pending' : 'completed';
                    break;
                case 'settlement':
                    $sale->status = 'completed';
                    break;
                case 'pending':
                    $sale->status = 'pending';
                    break;
                case 'deny':
                case 'expire':
                case 'cancel':
                    $sale->status = 'cancelled';
                    break;
            }

            $sale->payment_method = $type;
            $sale->save();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error:', ['message' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses notifikasi: ' . $e->getMessage()
            ], 500);
        }
    }
}
