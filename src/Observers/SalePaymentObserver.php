<?php

namespace Isotope\ShopBoss\Observers;

use Exception;
use Illuminate\Support\Str;
use Isotope\Finance\Models\FinanceRoot;
use Isotope\ShopBoss\Models\SalePayment;
use Isotope\Finance\Models\FinanceRecord;
use Isotope\Finance\Models\FinanceParticular;

class SalePaymentObserver
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

    public function created(SalePayment $salePayment)
    {
        if (class_exists(FinanceRecord::class)) {
            $receivable    = FinanceParticular::firstWhere('alias', 'receivable');
            $paymentMethod = $this->paymentMethod($salePayment->payment_method);
            
            FinanceRecord::entry([
                'description'     => "Create Payment of Sale : {$salePayment->reference}",
                'amount'          => $salePayment->amount,
                'reference_no'    => '',
                'recordable_type' => SalePayment::class,
                'recordable_id'   => $salePayment->id,
            ], $paymentMethod, 'increment');
            
            FinanceRecord::entry([
                'description'     => "Create Payment of Sale : {$salePayment->reference}",
                'amount'          => $salePayment->amount,
                'reference_no'    => '',
                'recordable_type' => SalePayment::class,
                'recordable_id'   => $salePayment->id,
            ], $receivable, 'decrement');
        }
    }

    public function updated(SalePayment $salePayment)
    {
        if (class_exists(FinanceRecord::class)) {
            $receivables = $salePayment->financeRecordFilter('receivable');
            $payments    = $salePayment->financeRecordFilter(Str::snake($salePayment->payment_method));

            $paymentAmount = 0;
            foreach ($payments as $payment) {
                $paymentAmount += ($payment->debit - $payment->credit);
            }
            $receivableAmount = 0;
            foreach ($receivables as $receivable) {
                $receivableAmount += ($receivable->debit - $receivable->credit);
            }

            $receivable    = FinanceParticular::firstWhere('alias', 'receivable');
            $paymentMethod = $this->paymentMethod($salePayment->payment_method);

            if ($paymentAmount != $salePayment->amount) {
                FinanceRecord::entry([
                    'description'     => "Update Payment of Sale : {$salePayment->reference}",
                    'amount'          => $paymentAmount < $salePayment->amount ? ($salePayment->amount - $paymentAmount) : ($paymentAmount - $salePayment->amount),
                    'reference_no'    => '',
                    'recordable_type' => SalePayment::class,
                    'recordable_id'   => $salePayment->id,
                ], $paymentMethod, $paymentAmount < $salePayment->amount ? 'increment' : 'decrement');
            }
            if ($receivableAmount != $salePayment->amount) {
                FinanceRecord::entry([
                    'description'     => "Update Payment of Sale : {$salePayment->reference}",
                    'amount'          => $receivableAmount < $salePayment->amount ? ($salePayment->amount - $receivableAmount) : ($receivableAmount - $salePayment->amount),
                    'reference_no'    => '',
                    'recordable_type' => SalePayment::class,
                    'recordable_id'   => $salePayment->id,
                ], $receivable, $receivableAmount < $salePayment->amount ? 'decrement' : 'increment');
            }
        }
    }


    public function deleted(SalePayment $salePayment)
    {
        if (class_exists(FinanceRecord::class)) {
            $receivable    = FinanceParticular::firstWhere('alias', 'receivable');
            $paymentMethod = $this->paymentMethod($salePayment->payment_method);
            
            FinanceRecord::entry([
                'description'     => "Delete Payment of Sale : {$salePayment->reference}",
                'amount'          => $salePayment->amount,
                'reference_no'    => '',
                'recordable_type' => SalePayment::class,
                'recordable_id'   => $salePayment->id,
            ], $paymentMethod, 'decrement');
            
            FinanceRecord::entry([
                'description'     => "Delete Payment of Sale : {$salePayment->reference}",
                'amount'          => $salePayment->amount,
                'reference_no'    => '',
                'recordable_type' => SalePayment::class,
                'recordable_id'   => $salePayment->id,
            ], $receivable, 'increment');
        }
    }
}
