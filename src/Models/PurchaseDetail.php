<?php

namespace Isotope\ShopBoss\Models;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseDetail extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['product'];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function purchase() {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }
}
