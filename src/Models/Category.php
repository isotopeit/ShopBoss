<?php

namespace Isotope\ShopBoss\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function products() {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
     public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
