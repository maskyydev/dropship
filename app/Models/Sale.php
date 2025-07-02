<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'sale_date',
        'total_amount',
        'total_tax',
        'total_discount',
        'grand_total',
        'payment_method',
        'status',
        'notes'
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'grand_total' => 'decimal:2'
    ];

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


    public static $statuses = [
        'completed' => 'Completed',
        'pending' => 'Pending',
        'cancelled' => 'Cancelled'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'sale_items')
                   ->withPivot('quantity', 'unit_price', 'discount', 'tax', 'subtotal');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['start_date'] ?? null, function ($query, $startDate) {
            $query->where('sale_date', '>=', $startDate);
        })->when($filters['end_date'] ?? null, function ($query, $endDate) {
            $query->where('sale_date', '<=', $endDate);
        })->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        })->when($filters['payment_method'] ?? null, function ($query, $paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        });
    }
}