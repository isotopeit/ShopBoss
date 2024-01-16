<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Isotope\ShopBoss\Models\Customer;
use Isotope\ShopBoss\Models\Purchase;
use Isotope\ShopBoss\Models\Sale;
use Isotope\ShopBoss\Models\Supplier;

class ReportsController extends Controller
{

    public static $permissions = [
        'profitLossReport'      => ['profitLossReport', 'ProfitLoss Report'],
        'paymentsReport'        => ['paymentsReport', 'Payments Report'],
        'salesReport'           => ['salesReport', 'Sales Report'],
        'purchasesReport'       => ['purchasesReport', 'Purchases Report'],
        'salesReturnReport'     => ['salesReturnReport', 'Sales Return Report'],
        'purchasesReturnReport' => ['purchasesReturnReport', 'Purchases Return Report']
    ];

    public function profitLossReport() {
        abort_if(Gate::denies('access_reports'), 403);
        return view('pos::reports.profit-loss.index');
    }

    public function paymentsReport() {
        abort_if(Gate::denies('access_reports'), 403);

        return view('pos::reports.payments.index');
    }

    public function salesReport() 
    {
        $from        = request()->from;
        $to          = request()->to;
        $data = [];
        if (!is_null($from) && !is_null($to)) {
            $data = Sale::query()
                    ->whereDate('date', '>=', request()->from)
                    ->whereDate('date', '<=', request()->to)
                    ->orderByDesc('created_at')
                    ->get();
        }

        if (request()->submit == 'print') 
        {
            return view('shopboss::reports.sales.print',compact('data','from','to'));
        }
        else
        {
            return view('shopboss::reports.sales.index',compact('data','from','to'));
        }
    }

    public function purchasesReport() {
        $from        = request()->from;
        $to          = request()->to;
        $data = [];
        if (!is_null($from) && !is_null($to)) {
            $data = Purchase::query()
                    ->whereDate('date', '>=', request()->from)
                    ->whereDate('date', '<=', request()->to)
                    ->orderByDesc('created_at')
                    ->get();
        }

        if (request()->submit == 'print') 
        {
            return view('shopboss::reports.purchases.print',compact('data','from','to'));
        }
        else
        {
            return view('shopboss::reports.purchases.index',compact('data','from','to'));
        }
    }

    public function salesReturnReport() {
        abort_if(Gate::denies('access_reports'), 403);
        $customers = Customer::all();
        return view('pos::reports.sales-return.index',compact('customers'));
    }

    public function purchasesReturnReport() {
        abort_if(Gate::denies('access_reports'), 403);

        return view('pos::reports.purchases-return.index');
    }
}
