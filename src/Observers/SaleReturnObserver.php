<?php

namespace Isotope\ShopBoss\Observers;

use Exception;
use Illuminate\Queue\Connectors\NullConnector;
use Illuminate\Support\Str;
use Isotope\Finance\Models\FinanceParticular;
use Isotope\Finance\Models\FinanceRecord;
use Isotope\Finance\Models\FinanceRoot;
use Isotope\ShopBoss\Models\SaleReturn;
use Isotope\Therapy\Models\Branch;

class SaleReturnObserver
{
    private function particularPayableCreate($branch_id)
    {
        $root = FinanceRoot::firstWhere('title', 'liability');
        if (is_null($root)) throw new Exception("Finance Root liability is not found", 404);

        $title = 'Payable';
        $alias = 'payable';

        if ($branch_id && settings()->enable_branch == 1) {
            $branch = Branch::find($branch_id);
            if ($branch) {
                $title = $branch->name . '- Payable';
                $alias = 'payable' . $branch_id;
            }
        }

        $data = FinanceParticular::create([
            'root_id'         => $root->id,
            'root_title'      => $root->title,
            'title'           => $title,
            'alias'           => $alias,
            'transactionable' => 0,
            'increment'       => $root->increment,
            'decrement'       => $root->decrement,
        ]);

        return $data;
    }

    private function paymentMethod($title)
    {
        $method = FinanceParticular::firstWhere('alias', $title);
        if (is_null($method)) {
            $root = FinanceRoot::firstWhere('title', 'asset');
            if (is_null($root)) throw new Exception("Finance Root asset is not found", 404);

            $method = FinanceParticular::create([
                'root_id'         => $root->id,
                'root_title'      => $root->title,
                'title'           => Str::headline($title),
                'alias'           => Str::snake($title),
                'transactionable' => 1,
                'increment'       => $root->increment,
                'decrement'       => $root->decrement,
            ]);
        }

        return $method;
    }

    private function particularSaleReturnCreate($branch_id=null)
    {
        $root = FinanceRoot::firstWhere('title', 'revenue');
        if(is_null($root)) throw new Exception("Finance Root revenue is not found", 404);

        $title = 'Sale Return';
        $alias = 'sale_return';

        if ($branch_id && settings()->enable_branch == 1) {
            $branch = Branch::find($branch_id);
            if ($branch) {
                $title = $branch->name . '- Sale Return';
                $alias = 'sale_return_' . $branch_id;
            }
        }
        
        $data = FinanceParticular::create([
            'root_id'         => $root->id,
            'root_title'      => $root->title,
            'title'           => $title,
            'alias'           => $alias,
            'transactionable' => 0,
            'increment'       => $root->increment,
            'decrement'       => $root->decrement,
        ]);

        return $data;
    }


    public function created(SaleReturn $saleReturn)
    {
        if (class_exists(FinanceRecord::class)) {

            $branch_id = null;
            if (settings()->enable_branch == 1 && $saleReturn->branch_id) {
                $branch_id = $saleReturn->branch_id;
            }

            $payable_alias = $branch_id && settings()->enable_branch == 1 ? 'payable_' . $branch_id : 'payable';
            $payable = FinanceParticular::firstWhere('alias', $payable_alias);
            if (is_null($payable)) {
                $payable = $this->particularPayableCreate($branch_id);
            }

      
            $revenue_alias = $branch_id && settings()->enable_branch == 1 ? 'sale_return_' . $branch_id : 'sale_return';
            $revenue = FinanceParticular::firstWhere('alias', $revenue_alias);
            if (is_null($revenue)) {
                $revenue = $this->particularSaleReturnCreate($branch_id);
            }

            $payment_method_particular = FinanceParticular::firstWhere('id',$saleReturn->payment_method);
            if (is_null($payment_method_particular)) {
                $payment_method_particular = $this->paymentMethod($saleReturn->payment_method);
            }

            if (is_null($payment_method_particular)) {
                throw new \Exception("Payment Method Particular not found", 404);
            }

            FinanceRecord::entry([
                'description'     => "Revenue of Create Sale Return : {$saleReturn->reference}",
                'amount'          => $saleReturn->total_amount,
                'reference_no'    => '',
                'recordable_type' => SaleReturn::class,
                'recordable_id'   => $saleReturn->id,
            ], $revenue, 'decrement');

            if ($saleReturn->paid_amount > 0 && !str_contains($payment_method_particular->alias, 'bank')) {
                FinanceRecord::entry([
                    'description'     => "Payment of Create Sale Return : {$saleReturn->reference}",
                    'amount'          => $saleReturn->paid_amount,
                    'reference_no'    => '',
                    'recordable_type' => SaleReturn::class,
                    'recordable_id'   => $saleReturn->id,
                ], $payment_method_particular, 'decrement');
            }

            if ($saleReturn->due_amount > 0) {
                FinanceRecord::entry([
                    'description'     => "Payment Due of Create Sale Return : {$saleReturn->reference}",
                    'amount'          => $saleReturn->due_amount,
                    'reference_no'    => '',
                    'recordable_type' => SaleReturn::class,
                    'recordable_id'   => $saleReturn->id,
                ], $payable, 'increment');
            }
        }
    }

    public function updated(SaleReturn $saleReturn)
    {
        if (class_exists(FinanceRecord::class)) {
            $payables = $saleReturn->financeRecord->where('particular_alias', 'payable');
            $revenues = $saleReturn->financeRecord->where('particular_alias', 'sale_return');

            $revenueAmount = 0;
            foreach ($revenues as $revenue) {
                $revenueAmount += ($revenue->debit - $revenue->credit);
            }
            $payableAmount = 0;
            foreach ($payables as $payable) {
                $payableAmount += ($payable->credit - $payable->debit);
            }

            $payable = FinanceParticular::firstWhere('alias', 'payable');
            $revenue = FinanceParticular::firstWhere('alias', 'sale_return');

            if ($revenueAmount != $saleReturn->total_amount) {
                FinanceRecord::entry([
                    'description'     => "Expense of Update Sale Return : {$saleReturn->reference}",
                    'amount'          => $payableAmount < $saleReturn->total_amount ? ($saleReturn->total_amount - $revenueAmount) : ($revenueAmount - $saleReturn->total_amount),
                    'reference_no'    => '',
                    'recordable_type' => SaleReturn::class,
                    'recordable_id'   => $saleReturn->id,
                ], $revenue, $revenueAmount < $saleReturn->total_amount ? 'decrement' : 'increment');
            }
            
            if($payableAmount != $saleReturn->due_amount) {
                FinanceRecord::entry([
                    'description'     => "Payment Due of Update Sale Return : {$saleReturn->reference}",
                    'amount'          => $payableAmount < $saleReturn->due_amount ? ($saleReturn->due_amount - $payableAmount) : ($payableAmount - $saleReturn->due_amount),
                    'reference_no'    => '',
                    'recordable_type' => SaleReturn::class,
                    'recordable_id'   => $saleReturn->id,
                ], $payable, $payableAmount < $saleReturn->due_amount ? 'increment' : 'decrement');
            }
        }
    }


    public function deleted(SaleReturn $saleReturn)
    {
        if (class_exists(FinanceRecord::class)) {
            $payable       = FinanceParticular::firstWhere('alias', 'payable');
            $paymentMethod = $this->paymentMethod($saleReturn->payment_method);
            $revenue       = FinanceParticular::firstWhere('alias', 'sale_return');
            
            FinanceRecord::entry([
                'description'     => "Revenue of Delete Sale Return : {$saleReturn->reference}",
                'amount'          => $saleReturn->total_amount,
                'reference_no'    => '',
                'recordable_type' => SaleReturn::class,
                'recordable_id'   => $saleReturn->id,
            ], $revenue, 'increment');
            
            if($saleReturn->paid_amount > 0) {
                FinanceRecord::entry([
                    'description'     => "Payment of Delete Sale Return : {$saleReturn->reference}",
                    'amount'          => $saleReturn->paid_amount,
                    'reference_no'    => '',
                    'recordable_type' => SaleReturn::class,
                    'recordable_id'   => $saleReturn->id,
                ], $paymentMethod, 'increment');
            }
            
            if($saleReturn->due_amount > 0) {
                FinanceRecord::entry([
                    'description'     => "Payment Due of Delete Sale Return : {$saleReturn->reference}",
                    'amount'          => $saleReturn->due_amount,
                    'reference_no'    => '',
                    'recordable_type' => SaleReturn::class,
                    'recordable_id'   => $saleReturn->id,
                ], $payable, 'decrement');
            }
        }
    }
}
