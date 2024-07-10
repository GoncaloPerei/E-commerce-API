<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'image',
    ];

    public function product(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProductStatus::class, 'product_status_id', 'id');
    }
}
