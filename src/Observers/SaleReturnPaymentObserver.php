<?php

namespace Isotope\ShopBoss\Observers;

use Exception;
use Illuminate\Support\Str;
use Isotope\Finance\Models\FinanceRoot;
use Isotope\Finance\Models\FinanceRecord;
use Isotope\Finance\Models\FinanceParticular;
use Isotope\ShopBoss\Models\SaleReturnPayment;

class SaleReturnPaymentObserver
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

    public function created(SaleReturnPayment $saleReturnPayment)
    {
        if (class_exists(FinanceRecord::class)) {
            $payable = FinanceParticular::firstWhere('alias', 'payable');
            $paymentMethod = $this->paymentMethod($saleReturnPayment->payment_method);
            
            FinanceRecord::entry([
                'description'     => "Create Payment of Sale Return : {$saleReturnPayment->reference}",
                'amount'          => $saleReturnPayment->amount,
                'reference_no'    => '',
                'recordable_type' => SaleReturnPayment::class,
                'recordable_id'   => $saleReturnPayment->id,
            ], $paymentMethod, 'decrement');
            
            FinanceRecord::entry([
                'description'     => "Payment Due of Sale Return : {$saleReturnPayment->reference}",
                'amount'          => $saleReturnPayment->amount,
                'reference_no'    => '',
                'recordable_type' => SaleReturnPayment::class,
                'recordable_id'   => $saleReturnPayment->id,
            ], $payable, 'decrement');
        }
    }

    public function updated(SaleReturnPayment $saleReturnPayment)
    {
        if (class_exists(FinanceRecord::class)) {
            $payables = $saleReturnPayment->financeRecord->where('particular_alias', 'payable');
            $payments = $saleReturnPayment->financeRecord->where('particular_alias', Str::snake($saleReturnPayment->payment_method));

            $paymentAmount = 0;
            foreach ($payments as $payment) {
                $paymentAmount += ($payment->debit - $payment->credit);
            }
            $payableAmount = 0;
            foreach ($payables as $payable) {
                $payableAmount += ($payable->credit - $payable->debit);
            }

            $payable       = FinanceParticular::firstWhere('alias', 'payable');
            $paymentMethod = $this->paymentMethod($saleReturnPayment->payment_method);

            if ($paymentAmount != $saleReturnPayment->amount) {
                FinanceRecord::entry([
                    'description'     => "Update Payment of Sale Return : {$saleReturnPayment->reference}",
                    'amount'          => $paymentAmount < $saleReturnPayment->amount ? ($saleReturnPayment->amount - $paymentAmount) : ($paymentAmount - $saleReturnPayment->amount),
                    'reference_no'    => '',
                    'recordable_type' => SaleReturnPayment::class,
                    'recordable_id'   => $saleReturnPayment->id,
                ], $paymentMethod, $paymentAmount < $saleReturnPayment->amount ? 'increment' : 'decrement');
            }
            
            if ($payableAmount != $saleReturnPayment->amount) {
                FinanceRecord::entry([
                    'description'     => "Update Payment of Sale Return : {$saleReturnPayment->reference}",
                    'amount'          => $payableAmount < $saleReturnPayment->amount ? ($saleReturnPayment->amount - $payableAmount) : ($payableAmount - $saleReturnPayment->amount),
                    'reference_no'    => '',
                    'recordable_type' => SaleReturnPayment::class,
                    'recordable_id'   => $saleReturnPayment->id,
                ], $payable, $payableAmount < $saleReturnPayment->amount ? 'increment' : 'decrement');
            }
        }
    }


    public function deleted(SaleReturnPayment $saleReturnPayment)
    {
        if (class_exists(FinanceRecord::class)) {
            $payable       = FinanceParticular::firstWhere('alias', 'payable');
            $paymentMethod = $this->paymentMethod($saleReturnPayment->payment_method);
            
            FinanceRecord::entry([
                'description'     => "Delete Payment of Sale Return : {$saleReturnPayment->reference}",
                'amount'          => $saleReturnPayment->amount,
                'reference_no'    => '',
                'recordable_type' => SaleReturnPayment::class,
                'recordable_id'   => $saleReturnPayment->id,
            ], $payable, 'increment');
            
            FinanceRecord::entry([
                'description'     => "Delete Payment of Sale Return : {$saleReturnPayment->reference}",
                'amount'          => $saleReturnPayment->amount,
                'reference_no'    => '',
                'recordable_type' => SaleReturnPayment::class,
                'recordable_id'   => $saleReturnPayment->id,
            ], $paymentMethod, 'increment');
        }
    }
}
