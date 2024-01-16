<?php

namespace Isotope\ShopBoss\Models;

use Illuminate\Support\Carbon;
use Isotope\ShopBoss\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleReturnPayment extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function saleReturn() {
        return $this->belongsTo(SaleReturn::class, 'sale_return_id', 'id');
    }

    public function getDateAttribute($value) {
        return Carbon::parse($value)->format('d M, Y');
    }

    public function scopeBySaleReturn($query) {
        return $query->where('sale_return_id', request()->route('sale_return_id'));
    }
}
