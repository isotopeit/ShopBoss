<?php

namespace Isotope\ShopBoss\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Isotope\ShopBoss\Models\Product;

class AdjustedProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['product'];

    public function adjustment() {
        return $this->belongsTo(Adjustment::class, 'adjustment_id', 'id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
