<?php

namespace App\Http\Controllers;

use App\Models\Shipping;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'recipient_name'   => 'required|string|max:100',
        'phone_number'     => 'required|string|max:20',
        'address'          => 'required|string',
        'province'         => 'required|string',
        'city'             => 'required|string',
        'subdistrict'      => 'required|string',
        'postal_code'      => 'required|string|max:10',
        'shipping_method'  => 'required|string',
    ]);

    $userId = Auth::id();

    // Cek payment terakhir milik user
    $payment = Payment::where('user_id', $userId)->latest('id')->first();

    if (!$payment) {
        return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan, silakan selesaikan pembayaran terlebih dahulu.');
    }

    Shipping::create([
        'user_id'         => $userId,
        'payment_id'      => $payment->id,
        'recipient_name'  => $request->recipient_name,
        'phone_number'    => $request->phone_number,
        'address'         => $request->address,
        'province'        => $request->province,
        'city'            => $request->city,
        'subdistrict'     => $request->subdistrict,
        'postal_code'     => $request->postal_code,
        'shipping_method' => $request->shipping_method,
        'shipping_cost'   => 0, // ← Baris ini aman, bukan penyebab error
        'status'          => 'pending',
    ]);

    return redirect()->route('pembayaran.form', ['id' => $payment->id]);
}
}
