<?php

namespace Isotope\ShopBoss\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Isotope\ShopBoss\Models\Product;

class QuotationDetails extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['product'];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function quotation() {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }

    public function getPriceAttribute($value) {
        return $value / 100;
    }

    public function getUnitPriceAttribute($value) {
        return $value / 100;
    }

    public function getSubTotalAttribute($value) {
        return $value / 100;
    }

    public function getProductDiscountAmountAttribute($value) {
        return $value / 100;
    }

    public function getProductTaxAmountAttribute($value) {
        return $value / 100;
    }}
