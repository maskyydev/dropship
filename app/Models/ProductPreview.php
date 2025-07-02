<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPreview extends Model
{
    protected $fillable = ['product_id', 'url'];

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
