<?php

namespace Isotope\ShopBoss\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BranchUser extends Pivot
{
    protected $table = 'branch_user';

    protected $fillable = [
        'user_id',
        'branch_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    

    public function branches()
    {
        return $this->belongsToMany(\Isotope\ShopBoss\Models\Branch::class, 'branch_user', 'user_id', 'branch_id');
    }

    public function getBranchAttribute()
    {
        return $this->branches()->first();
    }
}
