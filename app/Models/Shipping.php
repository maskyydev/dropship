<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipping extends Model
{
    protected $fillable = [
        'user_id',
        'payment_id',
        'recipient_name',
        'phone_number',
        'address',
        'subdistrict',
        'city',
        'province',
        'postal_code',
        'shipping_method',
        'shipping_cost',
        'tracking_number',
        'status'
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2'
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}