<?php

namespace Isotope\ShopBoss\Observers;

use Exception;
use Illuminate\Support\Str;
use Isotope\Finance\Models\FinanceRoot;
use Isotope\Finance\Models\FinanceRecord;
use Isotope\Finance\Models\FinanceParticular;
use Isotope\ShopBoss\Models\PurchasePayment;

class PurchasePaymentObserver
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

    public function created(PurchasePayment $purchasePayment)
    {
        if (class_exists(FinanceRecord::class)) {
            $payable = FinanceParticular::firstWhere('alias', 'payable');
            $paymentMethod = $this->paymentMethod($purchasePayment->payment_method);
            
            FinanceRecord::entry([
                'description'     => "Create Payment of Purchase : {$purchasePayment->reference}",
                'amount'          => $purchasePayment->amount,
                'reference_no'    => '',
                'recordable_type' => PurchasePayment::class,
                'recordable_id'   => $purchasePayment->id,
            ], $paymentMethod, 'decrement');
            
            FinanceRecord::entry([
                'description'     => "Create Payment of Purchase : {$purchasePayment->reference}",
                'amount'          => $purchasePayment->amount,
                'reference_no'    => '',
                'recordable_type' => PurchasePayment::class,
                'recordable_id'   => $purchasePayment->id,
            ], $payable, 'decrement');
        }
    }

    public function updated(PurchasePayment $purchasePayment)
    {
        if (class_exists(FinanceRecord::class)) {
            $payables = $purchasePayment->financeRecordFilter('payable');
            $payments = $purchasePayment->financeRecordFilter(Str::snake($purchasePayment->payment_method));

            $paymentAmount = 0;
            foreach ($payments as $payment) {
                $paymentAmount += ($payment->debit - $payment->credit);
            }
            $payableAmount = 0;
            foreach ($payables as $payable) {
                $payableAmount += ($payable->credit - $payable->debit);
            }

            $payable       = FinanceParticular::firstWhere('alias', 'payable');
            $paymentMethod = $this->paymentMethod($purchasePayment->payment_method);

            if ($paymentAmount != $purchasePayment->amount) {
                FinanceRecord::entry([
                    'description'     => "Update Payment of Purchase : {$purchasePayment->reference}",
                    'amount'          => $paymentAmount < $purchasePayment->amount ? ($purchasePayment->amount - $paymentAmount) : ($paymentAmount - $purchasePayment->amount),
                    'reference_no'    => '',
                    'recordable_type' => PurchasePayment::class,
                    'recordable_id'   => $purchasePayment->id,
                ], $paymentMethod, $paymentAmount < $purchasePayment->amount ? 'increment' : 'decrement');
            }
            
            if ($payableAmount != $purchasePayment->amount) {
                FinanceRecord::entry([
                    'description'     => "Update Payment of Purchase : {$purchasePayment->reference}",
                    'amount'          => $payableAmount < $purchasePayment->amount ? ($purchasePayment->amount - $payableAmount) : ($payableAmount - $purchasePayment->amount),
                    'reference_no'    => '',
                    'recordable_type' => PurchasePayment::class,
                    'recordable_id'   => $purchasePayment->id,
                ], $payable, $payableAmount < $purchasePayment->amount ? 'increment' : 'decrement');
            }
        }
    }


    public function deleted(PurchasePayment $purchasePayment)
    {
        if (class_exists(FinanceRecord::class)) {
            $payable       = FinanceParticular::firstWhere('alias', 'payable');
            $paymentMethod = $this->paymentMethod($purchasePayment->payment_method);
            
            FinanceRecord::entry([
                'description'     => "Delete Payment of Purchase : {$purchasePayment->reference}",
                'amount'          => $purchasePayment->amount,
                'reference_no'    => '',
                'recordable_type' => PurchasePayment::class,
                'recordable_id'   => $purchasePayment->id,
            ], $payable, 'increment');
            
            FinanceRecord::entry([
                'description'     => "Delete Payment of Purchase : {$purchasePayment->reference}",
                'amount'          => $purchasePayment->amount,
                'reference_no'    => '',
                'recordable_type' => PurchasePayment::class,
                'recordable_id'   => $purchasePayment->id,
            ], $paymentMethod, 'increment');
        }
    }
}
