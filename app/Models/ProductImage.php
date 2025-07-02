<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // ✅ Tambahkan ini

class ProductImage extends Model
{
    use HasFactory, SoftDeletes; // ✅ Tambahkan SoftDeletes

    protected $fillable = [
        'product_id',
        'filename',
    ];

    /**
     * Relasi ke produk induk.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
