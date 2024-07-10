<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'price',
        'image',
        'stock',
        'product_category_id',
        'product_status_id',
    ];

    public function getPriceAttribute($value)
    {
        return number_format($value, 2, '.', '');
    }

    public function scopePriceRange(Builder $query, $minPrice, $maxPrice)
    {
        if (!is_null($minPrice)) {
            $query->where('price', '>=', $minPrice);
        }
        if (!is_null($maxPrice)) {
            $query->where('price', '<=', $maxPrice);
        }
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProductStatus::class, 'product_status_id', 'id');
    }
}
