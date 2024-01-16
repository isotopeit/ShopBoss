<?php

namespace Isotope\ShopBoss\Models;

use Isotope\ShopBoss\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends BaseModel
{
    use HasFactory;
    protected $with = ['uploads'];

    protected $guarded = [];
    protected $casts = [
        'id' => 'string',
    ];

    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function uploads() {
        return $this->morphMany(Upload::class, 'uploadable');
    }

}
