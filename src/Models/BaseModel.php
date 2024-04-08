<?php

namespace Isotope\ShopBoss\Models;

use Isotope\Metronic\Models\MasterModel;
use Isotope\Finance\Models\FinanceRecord;

class BaseModel extends MasterModel
{
    public function financeRecord()
    {
        return $this->morphMany(FinanceRecord::class, 'recordable');
    }
}
