<?php

namespace Isotope\ShopBoss\Models;

use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleDetails extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['product'];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function sale() {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }
}
