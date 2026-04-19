<?php

namespace Isotope\ShopBoss\Observers;

use Exception;
use Illuminate\Support\Str;
use Isotope\Finance\Models\BankTransaction;
use Isotope\Finance\Models\FinanceParticular;
use Isotope\Finance\Models\FinanceRecord;
use Isotope\Finance\Models\FinanceRoot;
use Isotope\ShopBoss\Models\Purchase;
use Isotope\Therapy\Models\Branch;

class PurchaseObserver
{
    private function particularPayableCreate($branch_id)
    {
        $root = FinanceRoot::firstWhere('title', 'liability');
        if(is_null($root)) throw new Exception("Finance Root liability is not found", 404);

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

    private function particularPurchaseCreate($branch_id)
    {
        $root = FinanceRoot::firstWhere('title', 'expense');
        if(is_null($root)) throw new Exception("Finance Root expense is not found", 404);

        $title = 'Purchase';
        $alias = 'purchase';

        if ($branch_id && settings()->enable_branch == 1) {
            $branch = Branch::find($branch_id);
            if ($branch) {
                $title = $branch->name . '- Purchase';
                $alias = 'purchase' . $branch_id;
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

    public function created(Purchase $purchase)
    {
        if (class_exists(FinanceRecord::class)) {

            $branch_id = null;
            if (settings()->enable_branch == 1 && $purchase->branch_id) {
                $branch_id = $purchase->branch_id;
            }


            $payable_alias = $branch_id && settings()->enable_branch == 1 ? 'payable_' . $branch_id : 'payable';
            $payable = FinanceParticular::firstWhere('alias', $payable_alias);
            if(is_null($payable)) {
                $payable = $this->particularPayableCreate($branch_id);
            }

            $expense_alias = $branch_id && settings()->enable_branch == 1 ? 'purchase_' . $branch_id : 'purchase';
            $expense = FinanceParticular::firstWhere('alias', $expense_alias);
            if(is_null($expense)) {
                $expense = $this->particularPurchaseCreate($branch_id);
            }

            $payment_method_perticular = FinanceParticular::firstWhere('id', $purchase->payment_method);
            if (is_null($payment_method_perticular)) {
                throw new Exception("Payment Method Particular not found", 404);
            }

            FinanceRecord::entry([
                'description'     => "Expense of Create Purchase : {$purchase->reference}",
                'amount'          => $purchase->total_amount,
                'reference_no'    => '',
                'recordable_type' => Purchase::class,
                'recordable_id'   => $purchase->id,
            ], $expense, 'increment');

            if($purchase->paid_amount > 0 && !str_contains($payment_method_perticular->alias , 'bank')) {
                FinanceRecord::entry([
                    'description'     => "Payment of Create Purchase : {$purchase->reference}",
                    'amount'          => $purchase->paid_amount,
                    'reference_no'    => '',
                    'recordable_type' => Purchase::class,
                    'recordable_id'   => $purchase->id,
                ], $payment_method_perticular, 'decrement');
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
