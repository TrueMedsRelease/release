<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $table = 'category';

    public function product() : BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_category');
    }

    public function category_desc(): HasMany
    {
        return $this->hasMany(CategoryDesc::class);
    }
}
