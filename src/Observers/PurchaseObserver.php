<?php

namespace Isotope\ShopBoss\Observers;

use Exception;
use Illuminate\Support\Str;
use Isotope\ShopBoss\Models\Purchase;
use Isotope\Finance\Models\FinanceRoot;
use Isotope\Finance\Models\FinanceRecord;
use Isotope\Finance\Models\FinanceParticular;

class PurchaseObserver
{
    private function particularPayableCreate()
    {
        $root = FinanceRoot::firstWhere('title', 'liability');
        if(is_null($root)) throw new Exception("Finance Root liability is not found", 404);
        
        $data = FinanceParticular::create([
            'root_id'         => $root->id,
            'root_title'      => $root->title,
            'title'           => 'Payable',
            'alias'           => 'payable',
            'transactionable' => 0,
            'increment'       => $root->increment,
            'decrement'       => $root->decrement,
        ]);

        return $data;
    }

    private function paymentMethod($title)
    {
        $method = FinanceParticular::firstWhere('alias', $title);
        if(is_null($method)) {
            $root = FinanceRoot::firstWhere('title', 'asset');
            if(is_null($root)) throw new Exception("Finance Root asset is not found", 404);
            
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

    private function particularPurchaseCreate()
    {
        $root = FinanceRoot::firstWhere('title', 'expense');
        if(is_null($root)) throw new Exception("Finance Root expense is not found", 404);
        
        $data = FinanceParticular::create([
            'root_id'         => $root->id,
            'root_title'      => $root->title,
            'title'           => 'Purchase',
            'alias'           => 'purchase',
            'transactionable' => 0,
            'increment'       => $root->increment,
            'decrement'       => $root->decrement,
        ]);

        return $data;
    }

    public function created(Purchase $purchase)
    {
        if (class_exists(FinanceRecord::class)) {
            $payable = FinanceParticular::firstWhere('alias', 'payable');
            if(is_null($payable)) {
                $payable = $this->particularPayableCreate();
            }
            $paymentMethod = $this->paymentMethod($purchase->payment_method);
            $expense = FinanceParticular::firstWhere('alias', 'purchase');
            if(is_null($expense)) {
                $expense = $this->particularPurchaseCreate();
            }
            FinanceRecord::entry([
                'description'     => "Expense of Create Purchase : {$purchase->reference}",
                'amount'          => $purchase->total_amount,
                'reference_no'    => '',
                'recordable_type' => Purchase::class,
                'recordable_id'   => $purchase->id,
            ], $expense, 'increment');
            
            if($purchase->paid_amount > 0) {
                FinanceRecord::entry([
                    'description'     => "Payment of Create Purchase : {$purchase->reference}",
                    'amount'          => $purchase->paid_amount,
                    'reference_no'    => '',
                    'recordable_type' => Purchase::class,
                    'recordable_id'   => $purchase->id,
                ], $paymentMethod, 'decrement');
            }
            
            if($purchase->due_amount > 0) {
                FinanceRecord::entry([
                    'description'     => "Payment of Create Purchase : {$purchase->reference}",
                    'amount'          => $purchase->due_amount,
                    'reference_no'    => '',
                    'recordable_type' => Purchase::class,
                    'recordable_id'   => $purchase->id,
                ], $payable, 'increment');
            }
        }
    }

    public function updated(Purchase $purchase)
    {
        if (class_exists(FinanceRecord::class)) {
            $payables = $purchase->financeRecord->where('particular_alias', 'payable');
            $expenses = $purchase->financeRecord->where('particular_alias', 'purchase');

            $expenseAmount = 0;
            foreach ($expenses as $expense) {
                $expenseAmount += ($expense->debit - $expense->credit);
            }
            $payableAmount = 0;
            foreach ($payables as $payable) {
                $payableAmount += ($payable->credit - $payable->debit);
            }

            $payable = FinanceParticular::firstWhere('alias', 'payable');
            $expense = FinanceParticular::firstWhere('alias', 'expense');

            if ($expenseAmount != $purchase->total_amount) {
                FinanceRecord::entry([
                    'description'     => "Expense of Update Purchase : {$purchase->reference}",
                    'amount'          => $expenseAmount < $purchase->total_amount ? ($purchase->total_amount - $expenseAmount) : ($expenseAmount - $purchase->total_amount),
                    'reference_no'    => '',
                    'recordable_type' => Purchase::class,
                    'recordable_id'   => $purchase->id,
                ], $expense, $expenseAmount < $purchase->total_amount ? 'increment' : 'decrement');
            }
            
            if($payableAmount != $purchase->due_amount) {
                FinanceRecord::entry([
                    'description'     => "Payment Due of Update Purchase : {$purchase->reference}",
                    'amount'          => $payableAmount < $purchase->due_amount ? ($purchase->due_amount - $payableAmount) : ($payableAmount - $purchase->due_amount),
                    'reference_no'    => '',
                    'recordable_type' => Purchase::class,
                    'recordable_id'   => $purchase->id,
                ], $payable, $payableAmount < $purchase->due_amount ? 'increment' : 'decrement');
            }
        }
    }


    public function deleted(Purchase $purchase)
    {
        if (class_exists(FinanceRecord::class)) {
            $payable       = FinanceParticular::firstWhere('alias', 'payable');
            $paymentMethod = $this->paymentMethod($purchase->payment_method);
            $expense       = FinanceParticular::firstWhere('alias', 'purchase');
            
            FinanceRecord::entry([
                'description'     => "Expense of Delete Purchase : {$purchase->reference}",
                'amount'          => $purchase->total_amount,
                'reference_no'    => '',
                'recordable_type' => Purchase::class,
                'recordable_id'   => $purchase->id,
            ], $expense, 'decrement');
            
            if($purchase->paid_amount > 0) {
                FinanceRecord::entry([
                    'description'     => "Payment of Delete Purchase : {$purchase->reference}",
                    'amount'          => $purchase->paid_amount,
                    'reference_no'    => '',
                    'recordable_type' => Purchase::class,
                    'recordable_id'   => $purchase->id,
                ], $paymentMethod, 'increment');
            }
            
            if($purchase->due_amount > 0) {
                FinanceRecord::entry([
                    'description'     => "Payment of Delete Purchase : {$purchase->reference}",
                    'amount'          => $purchase->due_amount,
                    'reference_no'    => '',
                    'recordable_type' => Purchase::class,
                    'recordable_id'   => $purchase->id,
                ], $payable, 'decrement');
            }
        }
    }
}
