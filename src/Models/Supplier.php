<?php

namespace Isotope\ShopBoss\Models;

use Isotope\ShopBoss\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends BaseModel
{
    use HasFactory;

    protected $guarded = [];
    
    /**
     * Get the branch that owns the supplier.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
