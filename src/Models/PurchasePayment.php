<?php

namespace Isotope\ShopBoss\Models;

use Isotope\ShopBoss\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchasePayment extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function purchase() {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }

    public function scopeByPurchase($query) {
        return $query->where('purchase_id', request()->route('purchase_id'));
    }
}
