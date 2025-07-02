<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function pricing()
    {
        return view('payment.checkout', [
            'plans' => [
                'basic' => [
                    'name' => 'Basic',
                    'price' => 99000,
                    'features' => [
                        'Dashboard Dropship',
                        'Manajemen Produk',
                        'Laporan Penjualan',
                        'Dukungan Email'
                    ]
                ],
                'pro' => [
                    'name' => 'Pro',
                    'price' => 199000,
                    'features' => [
                        'Semua Fitur Basic',
                        'Integrasi Marketplace',
                        'Analisis Lanjutan',
                        'Dukungan Prioritas'
                    ]
                ],
                'enterprise' => [
                    'name' => 'Enterprise',
                    'price' => 399000,
                    'features' => [
                        'Semua Fitur Pro',
                        'API Access',
                        'Tim Dukungan Dedicated',
                        'Pelatihan Online'
                    ]
                ]
            ]
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,pro,enterprise'
        ]);

        $plans = [
            'basic' => ['price' => 99000, 'duration' => 30],
            'pro' => ['price' => 199000, 'duration' => 30],
            'enterprise' => ['price' => 399000, 'duration' => 30]
        ];

        $plan = $plans[$request->plan];
        $user = auth()->user();

        // Setup Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => 'DROPSHIP-' . uniqid(),
                'gross_amount' => $plan['price'],
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'item_details' => [
                [
                    'id' => $request->plan,
                    'price' => $plan['price'],
                    'quantity' => 1,
                    'name' => ucfirst($request->plan) . ' Plan Subscription',
                ]
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            
            // Simpan transaksi ke database
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_name' => ucfirst($request->plan) . ' Plan',
                'price' => $plan['price'],
                'start_date' => now(),
                'end_date' => now()->addDays($plan['duration']),
                'status' => 'pending'
            ]);

            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'transaction_id' => $params['transaction_details']['order_id'],
                'amount' => $plan['price'],
                'payment_method' => 'midtrans',
                'status' => 'pending',
                'midtrans_response' => json_encode(['snap_token' => $snapToken])
            ]);

            return view('payment.checkout-process', compact('snapToken', 'payment'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Payment gateway error: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $transactionId = $request->query('order_id');
        $payment = Payment::where('transaction_id', $transactionId)->firstOrFail();

        // Update payment status
        $payment->update(['status' => 'success']);

        // Update subscription
        $subscription = $payment->subscription;
        $subscription->update(['status' => 'active']);

        // Update user
        $user = $payment->user;
        $user->update([
            'is_subscribed' => true,
            'subscription_expiry' => $subscription->end_date
        ]);

        return view('payment.success', compact('payment'));
    }

    public function failed(Request $request)
    {
        $transactionId = $request->query('order_id');
        $payment = Payment::where('transaction_id', $transactionId)->firstOrFail();

        $payment->update(['status' => 'failed']);
        $payment->subscription->update(['status' => 'canceled']);

        return view('payment.failed', compact('payment'));
    }

    public function notification(Request $request)
    {
        // Handle Midtrans server-to-server notification
        // Implementasikan sesuai dokumentasi Midtrans
    }
}