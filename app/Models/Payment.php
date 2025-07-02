<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne; // ✅ INI YANG PENTING

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'transaction_id',
        'amount',
        'payment_method',
        'status',
        'midtrans_response'
    ];

    protected $casts = [
        'midtrans_response' => 'array',
        'amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function shipping(): HasOne
    {
        return $this->hasOne(Shipping::class);
    }
}
