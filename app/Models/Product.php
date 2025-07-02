<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image',
        'category',
        'barcode',
        'weight',
        'dimensions',
        'alamat',
        'recommend_percent'
    ];

    public static $categories = [
        'Elektronik',
        'Fashion',
        'Kesehatan',
        'Makanan & Minuman',
        'Olahraga',
        'Lainnya'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function files()
    {
        return $this->hasMany(ProductFile::class);
    }

    public function previews()
    {
        return $this->hasMany(ProductPreview::class);
    }

    public function marketing_files()
    {
        return $this->hasMany(ProductFile::class);
    }

    public function preview()
    {
        return $this->hasOne(ProductPreview::class);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('category', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('barcode', 'like', "%{$term}%");
        });
    }

    public function image()
    {
        return $this->hasOne(ProductImage::class);
    }
}