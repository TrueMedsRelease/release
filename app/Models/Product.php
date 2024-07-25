<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    public function product_desc(): HasMany
    {
        return $this->hasMany(ProductDesc::class);
    }

    public function product_packaging(): HasMany
    {
        return $this->hasMany(ProductPackaging::class);
    }

    public function category() : BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }
}
