<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductJual extends Model
{
    protected $table = 'product_jual';

    protected $fillable = [
        'user_id',
        'product_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'category',
        'barcode',
        'weight',
        'dimensions',
        'filter',
        'alamat',
        'recommend_percent',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
