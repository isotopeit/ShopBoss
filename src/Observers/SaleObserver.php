<?php

namespace Isotope\ShopBoss\Observers;

use Exception;
use Illuminate\Support\Str;
use Isotope\ShopBoss\Models\Sale;
use Isotope\Finance\Models\FinanceRoot;
use Isotope\Finance\Models\FinanceRecord;
use Isotope\Finance\Models\FinanceParticular;
use Isotope\Therapy\Models\Branch;


class SaleObserver
{
    private function particularReceivableCreate($branch_id = null)
    {
        $root = FinanceRoot::firstWhere('title', 'asset');
        if (is_null($root)) throw new Exception("Finance Root asset is not found", 404);

        $title = 'Receivable';
        $alias = 'receivable';

        if ($branch_id && settings()->enable_branch == 1) {
            $branch = Branch::find($branch_id);
            if ($branch) {
                $title = $branch->name . '- Receivable';
                $alias = 'receivable_' . $branch_id;
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

   private function particularSellCreate($branch_id = null)
    {
        $root = FinanceRoot::firstWhere('title', 'revenue');
        if (is_null($root)) throw new Exception("Finance Root revenue is not found", 404);

        $title = 'Sale Revenue';
        $alias = 'sale_revenue';

        if ($branch_id && settings()->enable_branch == 1) {
            $branch = Branch::find($branch_id);
            if ($branch) {
                $title = $branch->name . '- Sale Revenue';
                $alias = 'sale_revenue_' . $branch_id;
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

    public function created(Sale $sale)
    {
        if (class_exists(FinanceRecord::class)) {

            $branch_id = null;
            if (settings()->enable_branch == 1 && $sale->branch_id) {
                $branch_id = $sale->branch_id;
            }

            // Receivable
            $receivable_alias = $branch_id && settings()->enable_branch == 1 ? 'receivable_' . $branch_id : 'receivable';
            $receivable = FinanceParticular::firstWhere('alias', $receivable_alias);
            if (is_null($receivable)) {
                $receivable = $this->particularReceivableCreate($branch_id);
            }

            // Revenue
            $revenue_alias = $branch_id && settings()->enable_branch == 1 ? 'sale_revenue_' . $branch_id : 'sale_revenue';
            
            $revenue = FinanceParticular::firstWhere('alias', $revenue_alias);
            if (is_null($revenue)) {
                $revenue = $this->particularSellCreate($branch_id);
            }

            $payment_method_particular = FinanceParticular::firstWhere('id', $sale->payment_method);
            if (is_null($payment_method_particular)) {
                $payment_method_particular = $this->paymentMethod($sale->payment_method);
            }

            if (is_null($payment_method_particular)) {
                throw new \Exception("Payment Method Particular not found", 404);
            }

            FinanceRecord::entry([
                'description'     => "Revenue of Create Sale : {$sale->reference}",
                'amount'          => $sale->total_amount,
                'reference_no'    => '',
                'recordable_type' => Sale::class,
                'recordable_id'   => $sale->id,
            ], $revenue, 'increment');

            if ($sale->paid_amount > 0 && !str_contains($payment_method_particular->alias, 'bank')) {
                FinanceRecord::entry([
                    'description'     => "Payment of Create Sale : {$sale->reference}",
                    'amount'          => $sale->paid_amount,
                    'reference_no'    => '',
                    'recordable_type' => Sale::class,
                    'recordable_id'   => $sale->id,
                ], $payment_method_particular, 'increment');
            }

            if ($sale->due_amount > 0) {
                FinanceRecord::entry([
                    'description'     => "Due Amount of Create Sale : {$sale->reference}",
                    'amount'          => $sale->due_amount,
                    'reference_no'    => '',
                    'recordable_type' => Sale::class,
                    'recordable_id'   => $sale->id,
                ], $receivable, 'increment');
            }
        }
    }


    public function updated(Sale $sale)
    {
        if (class_exists(FinanceRecord::class)) {
            $receivables = $sale->financeRecord->where('particular_alias', 'receivable');
            $revenues    = $sale->financeRecord->where('particular_alias', 'sale_revenue');

            $receivableAmount = 0;
            foreach ($receivables as $receivable) {
                $receivableAmount += ($receivable->debit - $receivable->credit);
            }
            $revenueAmount = 0;
            foreach ($revenues as $revenue) {
                $revenueAmount += ($revenue->credit - $revenue->debit);
            }

            $revenue    = FinanceParticular::firstWhere('alias', 'sale_revenue');
            $receivable = FinanceParticular::firstWhere('alias', 'receivable');
            if ($revenueAmount != $sale->total_amount) {
                FinanceRecord::entry([
                    'description'     => "Revenue of Update Sale : {$sale->reference}",
                    'amount'          => $revenueAmount < $sale->total_amount ? ($sale->total_amount - $revenueAmount) : ($revenueAmount - $sale->total_amount),
                    'reference_no'    => '',
                    'recordable_type' => Sale::class,
                    'recordable_id'   => $sale->id,
                ], $revenue, $revenueAmount < $sale->total_amount ? 'increment' : 'decrement');
            }

            if($receivableAmount != $sale->due_amount) {
                FinanceRecord::entry([
                    'description'     => "Payment Due of Update Sale : {$sale->reference}",
                    'amount'          => $receivableAmount < $sale->due_amount ? ($sale->due_amount - $receivableAmount) : ($receivableAmount - $sale->due_amount),
                    'reference_no'    => '',
                    'recordable_type' => Sale::class,
                    'recordable_id'   => $sale->id,
                ], $receivable, $receivableAmount < $sale->due_amount ? 'increment' : 'decrement');
            }
        }
    }


    public function deleted(Sale $sale)
    {
        if (class_exists(FinanceRecord::class)) {
            $revenue       = FinanceParticular::firstWhere('alias', 'sale_revenue');
            $paymentMethod = $this->paymentMethod($sale->payment_method);
            $receivable    = FinanceParticular::firstWhere('alias', 'receivable');

            FinanceRecord::entry([
                'description'     => "Revenue of Delete Sale : {$sale->reference}",
                'amount'          => $sale->total_amount,
                'reference_no'    => '',
                'recordable_type' => Sale::class,
                'recordable_id'   => $sale->id,
            ], $revenue, 'decrement');

            if($sale->paid_amount > 0) {
                FinanceRecord::entry([
                    'description'     => "Payment of Delete Sale : {$sale->reference}",
                    'amount'          => $sale->paid_amount,
                    'reference_no'    => '',
                    'recordable_type' => Sale::class,
                    'recordable_id'   => $sale->id,
                ], $paymentMethod, 'decrement');
            }

            if($sale->due_amount > 0) {
                FinanceRecord::entry([
                    'description'     => "Payment Due of Delete Invoice : {$sale->reference}",
                    'amount'          => $sale->due_amount,
                    'reference_no'    => '',
                    'recordable_type' => Sale::class,
                    'recordable_id'   => $sale->id,
                ], $receivable, 'decrement');
            }
        }
    }
}
