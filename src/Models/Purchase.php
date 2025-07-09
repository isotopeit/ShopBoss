<?php

namespace Isotope\ShopBoss\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Isotope\ShopBoss\Observers\PurchaseObserver;

class Purchase extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function supplier() {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function purchaseDetails() {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id', 'id');
    }

    public function purchasePayments() {
        return $this->hasMany(PurchasePayment::class, 'purchase_id', 'id');
    }

    public function purchaseReturns() {
        return $this->hasMany(PurchaseReturn::class, 'purchase_id', 'id');
    }

    /**
     * Get the branch that owns the purchase.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $number = Purchase::max('id') + 1;
            $model->reference = make_reference_id('PR', $number);
        });
        static::created(fn($model) => (new PurchaseObserver())->created($model));
        static::updated(fn($model) => (new PurchaseObserver())->updated($model));
        static::deleted(fn($model) => (new PurchaseObserver())->deleted($model));
    }

    public function scopeCompleted($query) {
        return $query->where('status', 'Completed');
    }
}
