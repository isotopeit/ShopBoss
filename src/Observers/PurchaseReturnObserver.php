<?php

namespace Isotope\ShopBoss\Observers;

use Exception;
use Illuminate\Support\Str;
use Isotope\Finance\Models\FinanceRoot;
use Isotope\Finance\Models\FinanceRecord;
use Isotope\ShopBoss\Models\PurchaseReturn;
use Isotope\Finance\Models\FinanceParticular;

class PurchaseReturnObserver
{
    private function particularReceivableCreate()
    {
        $root = FinanceRoot::firstWhere('title', 'asset');
        if(is_null($root)) throw new Exception("Finance Root asset is not found", 404);
        
        $data = FinanceParticular::create([
            'root_id'         => $root->id,
            'root_title'      => $root->title,
            'title'           => 'Receivable',
            'alias'           => 'receivable',
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

    private function particularPurchaseReturnCreate()
    {
        $root = FinanceRoot::firstWhere('title', 'expense');
        if(is_null($root)) throw new Exception("Finance Root expense is not found", 404);
        
        $data = FinanceParticular::create([
            'root_id'         => $root->id,
            'root_title'      => $root->title,
            'title'           => 'Purchase Return',
            'alias'           => 'purchase_return',
            'transactionable' => 0,
            'increment'       => $root->increment,
            'decrement'       => $root->decrement,
        ]);

        return $data;
    }

    public function created(PurchaseReturn $purchaseReturn)
    {
        if (class_exists(FinanceRecord::class)) {
            $receivable = FinanceParticular::firstWhere('alias', 'receivable');
            if(is_null($receivable)) {
                $receivable = $this->particularReceivableCreate();
            }
            $paymentMethod = $this->paymentMethod($purchaseReturn->payment_method);
            $expense = FinanceParticular::firstWhere('alias', 'purchase_return');
            if(is_null($expense)) {
                $expense = $this->particularPurchaseReturnCreate();
            }
            FinanceRecord::entry([
                'description'     => "Expense of Create Purchase Return : {$purchaseReturn->reference}",
                'amount'          => $purchaseReturn->total_amount,
                'reference_no'    => '',
                'recordable_type' => PurchaseReturn::class,
                'recordable_id'   => $purchaseReturn->id,
            ], $expense, 'decrement');
            
            if($purchaseReturn->paid_amount > 0) {
                FinanceRecord::entry([
                    'description'     => "Payment of Create Purchase Return : {$purchaseReturn->reference}",
                    'amount'          => $purchaseReturn->paid_amount,
                    'reference_no'    => '',
                    'recordable_type' => PurchaseReturn::class,
                    'recordable_id'   => $purchaseReturn->id,
                ], $paymentMethod, 'increment');
            }
            
            if($purchaseReturn->due_amount > 0) {
                FinanceRecord::entry([
                    'description'     => "Payment Due of Create Purchase Return : {$purchaseReturn->reference}",
                    'amount'          => $purchaseReturn->due_amount,
                    'reference_no'    => '',
                    'recordable_type' => PurchaseReturn::class,
                    'recordable_id'   => $purchaseReturn->id,
                ], $receivable, 'increment');
            }
        }
    }

    public function updated(PurchaseReturn $purchaseReturn)
    {
        if (class_exists(FinanceRecord::class)) {
            $receivables = $purchaseReturn->financeRecordFilter('receivable');
            $expenses    = FinanceParticular::firstWhere('alias', 'purchase_return');

            $receivableAmount = 0;
            foreach ($receivables as $receivable) {
                $receivableAmount += ($receivable->debit - $receivable->credit);
            }
            $expenseAmount = 0;
            foreach ($expenses as $expense) {
                $expenseAmount += ($expense->credit - $expense->debit);
            }

            $expense    = FinanceParticular::firstWhere('alias', 'purchase_return');
            $receivable = FinanceParticular::firstWhere('alias', 'receivable');

            if ($expenseAmount != $purchaseReturn->total_amount) {
                FinanceRecord::entry([
                    'description'     => "Expense of Update Purchase Return : {$purchaseReturn->reference}",
                    'amount'          => $expenseAmount < $purchaseReturn->total_amount ? ($purchaseReturn->total_amount - $expenseAmount) : ($expenseAmount - $purchaseReturn->total_amount),
                    'reference_no'    => '',
                    'recordable_type' => PurchaseReturn::class,
                    'recordable_id'   => $purchaseReturn->id,
                ], $expense, $expenseAmount < $purchaseReturn->total_amount ? 'decrement' : 'increment');
            }
            
            if($receivableAmount != $purchaseReturn->due_amount) {
                FinanceRecord::entry([
                    'description'     => "Payment Due of Update Purchase Return : {$purchaseReturn->reference}",
                    'amount'          => $receivableAmount < $purchaseReturn->due_amount ? ($purchaseReturn->due_amount - $receivableAmount) : ($receivableAmount - $purchaseReturn->due_amount),
                    'reference_no'    => '',
                    'recordable_type' => PurchaseReturn::class,
                    'recordable_id'   => $purchaseReturn->id,
                ], $receivable, $receivableAmount < $purchaseReturn->due_amount ? 'increment' : 'decrement');
            }
        }
    }


    public function deleted(PurchaseReturn $purchaseReturn)
    {
        if (class_exists(FinanceRecord::class)) {
            $expense    = FinanceParticular::firstWhere('alias', 'purchase_return');
            $paymentMethod = $this->paymentMethod($purchaseReturn->payment_method);
            $receivable = FinanceParticular::firstWhere('alias', 'receivable');
    
            FinanceRecord::entry([
                'description'     => "Expense of Delete Purchase Return : {$purchaseReturn->reference}",
                'amount'          => $purchaseReturn->total_amount,
                'reference_no'    => '',
                'recordable_type' => PurchaseReturn::class,
                'recordable_id'   => $purchaseReturn->id,
            ], $expense, 'increment');
            
            if($purchaseReturn->paid_amount > 0) {
                FinanceRecord::entry([
                    'description'     => "Payment of Delete Purchase Return : {$purchaseReturn->reference}",
                    'amount'          => $purchaseReturn->paid_amount,
                    'reference_no'    => '',
                    'recordable_type' => PurchaseReturn::class,
                    'recordable_id'   => $purchaseReturn->id,
                ], $paymentMethod, 'decrement');
            }
            
            if($purchaseReturn->due_amount > 0) {
                FinanceRecord::entry([
                    'description'     => "Payment Due of Delete Purchase Return : {$purchaseReturn->reference}",
                    'amount'          => $purchaseReturn->due_amount,
                    'reference_no'    => '',
                    'recordable_type' => PurchaseReturn::class,
                    'recordable_id'   => $purchaseReturn->id,
                ], $receivable, 'decrement');
            }
        }
    }
}
