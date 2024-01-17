<?php

namespace Isotope\ShopBoss\Models;

use Isotope\Metronic\Models\MasterModel;
use Isotope\Finance\Models\FinanceRecord;

class BaseModel extends MasterModel
{
    public function financeRecordFilter($alias)
    {
        return $this->morphMany(FinanceRecord::class, 'recordable')->where('particular_alias', $alias);
    }
}
