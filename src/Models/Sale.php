<?php

namespace Isotope\ShopBoss\Models;

use Isotope\ShopBoss\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Isotope\ShopBoss\Observers\SaleObserver;

class Sale extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function saleDetails() {
        return $this->hasMany(SaleDetails::class, 'sale_id', 'id');
    }

    public function salePayments() {
        return $this->hasMany(SalePayment::class, 'sale_id', 'id');
    }
    public function saleReturn() {
        return $this->hasOne(SaleReturn::class, 'sale_id', 'id');
    }
    public function salesReturnDetails() {
        return $this->hasMany(SaleReturnDetail::class, 'sale_id', 'id');
    }
    
    /**
     * Get the branch that owns the sale.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $number = Sale::max('id') + 1;
            $model->reference = make_reference_id('SL', $number);
        });
        // static::created(fn($model) => (new SaleObserver())->created($model));
        // static::updated(fn($model) => (new SaleObserver())->updated($model));
        static::deleted(fn($model) => (new SaleObserver())->deleted($model));
    }
}
