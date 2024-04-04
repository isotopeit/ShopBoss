<?php

namespace Isotope\ShopBoss\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends BaseModel
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'shopboss_branches';
}
