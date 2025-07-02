<?php

namespace App\Http\Controllers;

use App\Models\{ProductJual, Product, Payment, Shipping, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Midtrans\{Config, Snap, Notification};

class PembayaranController extends Controller
{
    public function __construct()
    {
        Config::$serverKey     = config('midtrans.server_key');
        Config::$isProduction  = config('midtrans.is_production');
        Config::$isSanitized   = config('midtrans.is_sanitized');
        Config::$is3ds         = config('midtrans.is_3ds');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'products'              => 'required|array',
            'products.*.product_id' => 'required|exists:product_jual,id',
            'products.*.quantity'   => 'required|integer|min:1',
            'products.*.price'      => 'required|numeric',
            'products.*.name'       => 'required|string',
            'recipient_name'        => 'required|string|max:255',
            'phone_number'          => 'required|string|max:20',
            'address'               => 'required|string',
            'subdistrict'           => 'required|string',
            'city'                  => 'required|string',
            'province'              => 'required|string',
            'postal_code'           => 'required|string|max:10',
            'shipping_method'       => 'required|string',
        ]);

        $user = Auth::user();
        $products = $request->products;

        $subtotal = collect($products)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shippingCost = $this->calculateShippingCost($request->shipping_method);
        $totalAmount = $subtotal + $shippingCost;
        $transactionId = 'DROPSHIP-' . strtoupper(Str::random(13));

        // Buat Payment & Shipping
        $payment = Payment::create([
            'user_id'         => $user->id,
            'subscription_id' => $user->id,
            'transaction_id'  => $transactionId,
            'amount'          => $totalAmount,
            'payment_method'  => 'midtrans',
            'status'          => 'pending',
        ]);

        $shipping = Shipping::create([
            'user_id'         => $user->id, // ← INI WAJIB
            'payment_id'      => $payment->id,
            'recipient_name'  => $request->recipient_name,
            'phone_number'    => $request->phone_number,
            'address'         => $request->address,
            'subdistrict'     => $request->subdistrict,
            'city'            => $request->city,
            'province'        => $request->province,
            'postal_code'     => $request->postal_code,
            'shipping_method' => $request->shipping_method,
            'shipping_cost'   => $shippingCost,
            'status'          => 'pending',
        ]);

        // Kurangi stok
        foreach ($products as $item) {
            $jual = ProductJual::find($item['product_id']);
            if ($jual) {
                $jual->decrement('stock', $item['quantity']);
                if ($jual->product) {
                    $jual->product->decrement('stock', $item['quantity']);
                }
            }
        }

        // Siapkan Snap token awal (tanpa metode pembayaran tertentu)
        $itemDetails = [
            [
                'id'       => 'ONGKIR',
                'price'    => $shippingCost,
                'quantity' => 1,
                'name'     => 'Ongkir - ' . ucfirst($request->shipping_method),
            ]
        ];

        foreach ($products as $item) {
            $itemDetails[] = [
                'id'       => $item['product_id'],
                'price'    => $item['price'],
                'quantity' => $item['quantity'],
                'name'     => $item['name'],
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $transactionId,
                'gross_amount' => $totalAmount,
            ],
            'customer_details' => [
                'first_name' => $shipping->recipient_name,
                'email'      => $user->email,
                'phone'      => $shipping->phone_number,
                'shipping_address' => [
                    'first_name'   => $shipping->recipient_name,
                    'address'      => $shipping->address,
                    'city'         => $shipping->city,
                    'postal_code'  => $shipping->postal_code,
                    'phone'        => $shipping->phone_number,
                    'country_code' => 'IDN',
                ],
            ],
            'item_details' => $itemDetails,
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $payment->update(['midtrans_response' => json_encode(['snap_token' => $snapToken])]);

            return redirect()->route('pembayaran.checkout', ['id' => $payment->id]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function checkoutPage($id)
    {
        $payment = Payment::findOrFail($id);
        $shipping = Shipping::where('payment_id', $id)->firstOrFail();

        $snapToken = json_decode($payment->midtrans_response)->snap_token ?? null;

        return view('pembayaran.checkout', compact('payment', 'shipping', 'snapToken'));
    }

    private function calculateShippingCost($method)
    {
        $biaya = [
            'jne'       => 15000,
            'jnt'       => 18000,
            'sicepat'   => 16000,
            'pos'       => 12000,
            'same_day'  => 30000,
        ];
        return $biaya[strtolower($method)] ?? 10000;
    }

    public function getRedirectUrl(Request $request)
    {
        try {
            $request->validate([
                'payment_id' => 'required|exists:payments,id',
                'method'     => 'required|string',
            ]);

            $payment = Payment::findOrFail($request->payment_id);
            $payment->update(['payment_method' => $request->method]);

            $user     = Auth::user();
            $shipping = $payment->shipping;

            if (!$shipping) {
                throw new \Exception("Data shipping tidak ditemukan untuk payment ID: " . $payment->id);
            }

            // Daftar item yang akan dikirim ke Midtrans
            $itemDetails = [];

            // (Opsional) ambil produk dari relasi custom (disesuaikan dengan skema Anda)
            $products = ProductJual::where('user_id', $user->id)->get();

            foreach ($products as $item) {
                $itemDetails[] = [
                    'id'       => $item->id,
                    'price'    => $item->price,
                    'quantity' => 1, // Sesuaikan jika ada quantity
                    'name'     => $item->name ?? 'Produk',
                ];
            }

            // Tambahkan ongkir
            $itemDetails[] = [
                'id'       => 'ONGKIR',
                'price'    => $shipping->shipping_cost,
                'quantity' => 1,
                'name'     => 'Ongkir - ' . ucfirst($shipping->shipping_method),
            ];

            $params = [
                'transaction_details' => [
                    'order_id'     => $payment->transaction_id,
                    'gross_amount' => $payment->amount,
                ],
                'customer_details' => [
                    'first_name'       => $shipping->recipient_name,
                    'email'            => $user->email,
                    'phone'            => $shipping->phone_number,
                    'shipping_address' => [
                        'first_name'   => $shipping->recipient_name,
                        'address'      => $shipping->address,
                        'city'         => $shipping->city,
                        'postal_code'  => $shipping->postal_code,
                        'phone'        => $shipping->phone_number,
                        'country_code' => 'IDN',
                    ],
                ],
                'item_details' => $itemDetails,
                'enabled_payments' => [$request->method],
                'callbacks' => [
                    'finish'   => route('pembayaran.finish', ['payment_id' => $payment->id]),
                    'unfinish' => route('pembayaran.unfinish'),
                    'error'    => route('pembayaran.error'),
                ],
            ];

            $snapResponse = Snap::createTransaction($params);
            $payment->update(['midtrans_response' => json_encode($snapResponse)]);

            return response()->json(['redirect_url' => $snapResponse->redirect_url]);

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function notificationHandler(Request $request)
{
    try {
        // Ambil semua data request (baik dari Midtrans maupun Postman lokal)
        $payload = $request->all();

        // Jika kosong (misal dikirim dari server Midtrans langsung), pakai objek Notification()
        if (empty($payload)) {
            $notif = new \Midtrans\Notification();
            $payload = [
                'order_id'           => $notif->order_id,
                'transaction_status' => $notif->transaction_status,
                'payment_type'       => $notif->payment_type,
                'fraud_status'       => $notif->fraud_status,
                'raw'                => $notif
            ];
        }

        // Ambil data penting
        $orderId           = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $paymentType       = $payload['payment_type'] ?? null;
        $fraudStatus       = $payload['fraud_status'] ?? null;

        if (!$orderId) {
            return response()->json(['error' => 'order_id tidak ditemukan'], 400);
        }

        // Cari data payment berdasarkan transaction_id
        $payment = Payment::where('transaction_id', $orderId)->first();

        if (!$payment) {
            Log::warning("Transaksi tidak ditemukan untuk order_id: " . $orderId);
            return response()->json(['error' => 'Transaksi tidak ditemukan'], 404);
        }

        // Update respons Midtrans dan status pembayaran
        $payment->update([
            'midtrans_response' => json_encode($payload),
        ]);

        // Cek status dan update ke DB
        switch ($transactionStatus) {
            case 'capture':
                if ($paymentType === 'credit_card' && $fraudStatus === 'challenge') {
                    $payment->update(['status' => 'challenge']);
                } else {
                    $payment->update(['status' => 'success']);
                }
                break;

            case 'settlement':
                $payment->update(['status' => 'success']);
                break;

            case 'pending':
                $payment->update(['status' => 'pending']);
                break;

            case 'deny':
                $payment->update(['status' => 'denied']);
                break;

            case 'expire':
                $payment->update(['status' => 'expired']);
                break;

            case 'cancel':
                $payment->update(['status' => 'canceled']);
                break;

            default:
                Log::warning("Status transaksi tidak dikenali: " . $transactionStatus);
                break;
        }

        return response()->json(['status' => 'OK']);
    } catch (\Exception $e) {
        Log::error("Gagal memproses notifikasi Midtrans: " . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    protected function handleSuccessPayment(Payment $payment)
    {
        $payment->update([
            'status'  => 'success',
            'paid_at' => now(),
        ]);

        if ($payment->shipping) {
            $payment->shipping->update(['status' => 'processing']);
        }
    }

    public function finish(Request $request)
    {
        $payment = Payment::with('shipping')->findOrFail($request->payment_id);

        if ($payment->status === 'success') {
            return view('pembayaran.finish', compact('payment'));
        }

        return redirect()->route('pembayaran.unfinish');
    }

    public function unfinish()
    {
        return view('pembayaran.unfinish');
    }

    public function error()
    {
        return view('pembayaran.error');
    }
}
