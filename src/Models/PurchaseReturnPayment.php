<?php

namespace Isotope\ShopBoss\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class PurchaseReturnPayment extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function purchaseReturn() {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id', 'id');
    }
}
