<?php

namespace Isotope\ShopBoss\Models;

use Illuminate\Support\Carbon;
use Isotope\ShopBoss\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalePayment extends BaseModel
{

    use HasFactory;

    protected $guarded = [];

    public function sale() {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function scopeBySale($query) {
        return $query->where('sale_id', request()->route('sale_id'));
    }
}
