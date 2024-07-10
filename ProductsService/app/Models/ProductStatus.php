<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductStatus extends Model
{
    use HasFactory;

    public function product(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function category(): HasMany
    {
        return $this->hasMany(ProductCategory::class);
    }
}
