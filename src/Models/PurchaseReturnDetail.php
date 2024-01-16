<?php

namespace Isotope\ShopBoss\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Isotope\ShopBoss\Models\Product;

class PurchaseReturnDetail extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function purchaseReturn() {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id', 'id');
    }   

    public function purchaseDetails() {
        return $this->belongsTo(PurchaseDetail::class, 'purchase_detail_id', 'id');
    }   
}
