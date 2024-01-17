<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Isotope\ShopBoss\Models\Customer;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\Purchase;
use Isotope\ShopBoss\Models\PurchaseDetail;
use Isotope\ShopBoss\Models\Sale;
use Isotope\ShopBoss\Models\SaleDetails;
use Isotope\ShopBoss\Models\Supplier;

class ReportsController extends Controller
{

    public static $permissions = [
        'profitLossReport'          => ['profitLossReport', 'ProfitLoss Report'],
        'paymentsReport'            => ['paymentsReport', 'Payments Report'],
        'salesReport'               => ['salesReport', 'Sales Report'],
        'purchasesReport'           => ['purchasesReport', 'Purchases Report'],
        'salesReturnReport'         => ['salesReturnReport', 'Sales Return Report'],
        'purchasesReturnReport'     => ['purchasesReturnReport', 'Purchases Return Report'],
        'productWiseSeleReport'     => ['productWiseSeleReport', 'Product Wise Sele Report'],
        'productWisePurchaseReport' => ['productWisePurchaseReport', 'Product Wise Purchase Report'],
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
    public function productWiseSeleReport(Request $request) 
    {
        $from        = $request->from;
        $to          = $request->to;
        $product_id = $request->product_id ?? ['0'];
        if(!is_array($request->product_id) && !is_null($product_id))
        {
            $product_id = explode(',',$product_id);
        }

        $products = Product::selectRaw('id,product_name as text')->get();
        $data = [];
        if (!is_null($from) && !is_null($to) && !is_null($request->product_id)) 
        {
            $data = SaleDetails::query()
                        ->join('sales','sales.id','sale_details.sale_id')
                        ->selectRaw('
                            sales.date,
                            sales.reference,
                            sales.customer_name,
                            sale_details.*
                        ')
                        ->whereDate('sales.date', '>=', request()->from)
                        ->whereDate('sales.date', '<=', request()->to)
                        ->whereIn('sale_details.product_id',$product_id)
                        ->get()
                        ->groupBy('product_code');
        }

        if (request()->submit == 'print') 
        {
            return view('shopboss::reports.product-wise-sale.print',compact('data','from','to','products','product_id'));
        }
        else
        {
            return view('shopboss::reports.product-wise-sale.index',compact('data','from','to','products','product_id'));
        }
    }

    public function productWisePurchaseReport(Request $request) 
    {
        $from        = $request->from;
        $to          = $request->to;
        $product_id = $request->product_id ?? ['0'];
        if(!is_array($request->product_id) && !is_null($request->product_id))
        {
            $product_id = explode(',',$product_id);
        }

        $products = Product::selectRaw('id,product_name as text')->get();
        $data = [];
        if (!is_null($from) && !is_null($to) && !is_null($product_id)) 
        {
            $data = PurchaseDetail::query()
                        ->join('purchases','purchases.id','purchase_details.purchase_id')
                        ->selectRaw('
                            purchases.date,
                            purchases.reference,
                            purchases.supplier_name,
                            purchase_details.*
                        ')
                        ->whereDate('purchases.date', '>=', request()->from)
                        ->whereDate('purchases.date', '<=', request()->to)
                        ->whereIn('purchase_details.product_id',$product_id)
                        ->get()
                        ->groupBy('product_code');
        }

        if (request()->submit == 'print') 
        {
            return view('shopboss::reports.product-wise-purchase.print',compact('data','from','to','products','product_id'));
        }
        else
        {
            return view('shopboss::reports.product-wise-purchase.index',compact('data','from','to','products','product_id'));
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
