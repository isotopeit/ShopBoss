<?php

namespace Isotope\ShopBoss\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseReturn extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function supplier() {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    
    public function purchase() {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function purchaseReturnDetails() {
        return $this->hasMany(PurchaseReturnDetail::class, 'purchase_return_id', 'id');
    }

    public function purchaseReturnPayments() {
        return $this->hasMany(PurchaseReturnPayment::class, 'purchase_return_id', 'id');
    }

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $number = PurchaseReturn::max('id') + 1;
            $model->reference = make_reference_id('PRRN', $number);
        });
    }

    public function scopeCompleted($query) {
        return $query->where('status', 'Completed');
    }
}
