<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFile extends Model
{
    protected $fillable = ['product_id', 'filename'];

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
