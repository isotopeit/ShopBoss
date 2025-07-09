<?php

namespace Isotope\ShopBoss\Models;

use Isotope\ShopBoss\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Isotope\ShopBoss\Observers\SaleReturnObserver;

class SaleReturn extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function saleReturnDetails() {
        return $this->hasMany(SaleReturnDetail::class, 'sale_return_id', 'id');
    }

    public function saleReturnPayments() {
        return $this->hasMany(SaleReturnPayment::class, 'sale_return_id', 'id');
    }
    
    /**
     * Get the branch that owns the sale return.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $number = SaleReturn::max('id') + 1;
            $model->reference = make_reference_id('SLRN', $number);;
        });
        static::created(fn($model) => (new SaleReturnObserver())->created($model));
        static::updated(fn($model) => (new SaleReturnObserver())->updated($model));
        static::deleted(fn($model) => (new SaleReturnObserver())->deleted($model));
    }

    public function scopeCompleted($query) {
        return $query->where('status', 'Completed');
    }
}
