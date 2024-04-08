<?php

namespace Isotope\ShopBoss\Observers;

use Exception;
use Illuminate\Support\Str;
use Isotope\Finance\Models\FinanceRoot;
use Isotope\Finance\Models\FinanceRecord;
use Isotope\Finance\Models\FinanceParticular;
use Isotope\ShopBoss\Models\PurchaseReturnPayment;

class PurchaseReturnPaymentObserver
{
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

    public function created(PurchaseReturnPayment $purchaseReturnPayment)
    {
        if (class_exists(FinanceRecord::class)) {
            $receivable    = FinanceParticular::firstWhere('alias', 'receivable');
            $paymentMethod = $this->paymentMethod($purchaseReturnPayment->payment_method);
            
            FinanceRecord::entry([
                'description'     => "Create Payment of Purchase Return : {$purchaseReturnPayment->reference}",
                'amount'          => $purchaseReturnPayment->amount,
                'reference_no'    => '',
                'recordable_type' => PurchaseReturnPayment::class,
                'recordable_id'   => $purchaseReturnPayment->id,
            ], $paymentMethod, 'increment');
            
            FinanceRecord::entry([
                'description'     => "Create Payment of Purchase Return : {$purchaseReturnPayment->reference}",
                'amount'          => $purchaseReturnPayment->amount,
                'reference_no'    => '',
                'recordable_type' => PurchaseReturnPayment::class,
                'recordable_id'   => $purchaseReturnPayment->id,
            ], $receivable, 'decrement');
        }
    }

    public function updated(PurchaseReturnPayment $purchaseReturnPayment)
    {
        if (class_exists(FinanceRecord::class)) {
            $receivables = $purchaseReturnPayment->financeRecord->where('particular_alias', 'receivable');
            $payments    = $purchaseReturnPayment->financeRecord->where('particular_alias', Str::snake($purchaseReturnPayment->payment_method));

            $paymentAmount = 0;
            foreach ($payments as $payment) {
                $paymentAmount += ($payment->debit - $payment->credit);
            }
            $receivableAmount = 0;
            foreach ($receivables as $receivable) {
                $receivableAmount += ($receivable->debit - $receivable->credit);
            }

            $receivable    = FinanceParticular::firstWhere('alias', 'receivable');
            $paymentMethod = $this->paymentMethod($purchaseReturnPayment->payment_method);

            if ($paymentAmount != $purchaseReturnPayment->amount) {
                FinanceRecord::entry([
                    'description'     => "Update Payment of Purchase Return : {$purchaseReturnPayment->reference}",
                    'amount'          => $paymentAmount < $purchaseReturnPayment->amount ? ($purchaseReturnPayment->amount - $paymentAmount) : ($paymentAmount - $purchaseReturnPayment->amount),
                    'reference_no'    => '',
                    'recordable_type' => PurchaseReturnPayment::class,
                    'recordable_id'   => $purchaseReturnPayment->id,
                ], $paymentMethod, $paymentAmount < $purchaseReturnPayment->amount ? 'increment' : 'decrement');
            }
            if ($receivableAmount != $purchaseReturnPayment->amount) {
                FinanceRecord::entry([
                    'description'     => "Update Payment of Purchase Return : {$purchaseReturnPayment->reference}",
                    'amount'          => $receivableAmount < $purchaseReturnPayment->amount ? ($purchaseReturnPayment->amount - $receivableAmount) : ($receivableAmount - $purchaseReturnPayment->amount),
                    'reference_no'    => '',
                    'recordable_type' => PurchaseReturnPayment::class,
                    'recordable_id'   => $purchaseReturnPayment->id,
                ], $receivable, $receivableAmount < $purchaseReturnPayment->amount ? 'decrement' : 'increment');
            }
        }
    }


    public function deleted(PurchaseReturnPayment $purchaseReturnPayment)
    {
        if (class_exists(FinanceRecord::class)) {
            $receivable    = FinanceParticular::firstWhere('alias', 'receivable');
            $paymentMethod = $this->paymentMethod($purchaseReturnPayment->payment_method);
            
            FinanceRecord::entry([
                'description'     => "Delete Payment of Purchase Return : {$purchaseReturnPayment->reference}",
                'amount'          => $purchaseReturnPayment->total_amount,
                'reference_no'    => '',
                'recordable_type' => PurchaseReturnPayment::class,
                'recordable_id'   => $purchaseReturnPayment->id,
            ], $paymentMethod, 'decrement');
            
            FinanceRecord::entry([
                'description'     => "Delete Payment of Purchase Return : {$purchaseReturnPayment->reference}",
                'amount'          => $purchaseReturnPayment->amount,
                'reference_no'    => '',
                'recordable_type' => PurchaseReturnPayment::class,
                'recordable_id'   => $purchaseReturnPayment->id,
            ], $receivable, 'increment');
        }
    }
}
