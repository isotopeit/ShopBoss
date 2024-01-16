<?php

namespace Isotope\ShopBoss\Models;

use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleReturnDetail extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['product'];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function saleReturn() {
        return $this->belongsTo(SaleReturnPayment::class, 'sale_return_id', 'id');
    }

    public function saleDetails() {
        return $this->belongsTo(SaleDetails::class, 'sale_detail_id', 'id');
    }

}
