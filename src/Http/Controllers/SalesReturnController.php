<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\Customer;
use Isotope\ShopBoss\Models\SaleReturn;
use Isotope\ShopBoss\Models\SaleDetails;
use Isotope\ShopBoss\Models\PurchaseDetail;
use Isotope\ShopBoss\Models\SaleReturnDetail;
use Isotope\ShopBoss\Models\SaleReturnPayment;

class SalesReturnController extends Controller
{
    public static $permissions = [
        'index'   => ['access_sale_returns', 'Sale Return List'],
        'create'  => ['create_sale_returns', 'Sale Return Create'],
        'store'   => ['store_sale_returns', 'Sale Return Store'],
        'show'    => ['show_sale_returns', 'Sale Return Store'],
        'edit'    => ['edit_sale_returns', 'Sale Return Edit'],
        'update'  => ['update_sale_returns', 'Sale Return Update'],
        'destroy' => ['delete_sale_returns', 'Sale Return Delete'],
    ];

    public function pdf($id)
    {
        $saleReturn = SaleReturn::findOrFail($id);
        $customer   = Customer::findOrFail($saleReturn->customer_id);

        $pdf = \PDF::loadview('pos::print', [
            'sale_return' => $saleReturn,
            'customer' => $customer,
        ])->setPaper('a4');

        return $pdf->stream('sale-return-'. $saleReturn->reference .'.pdf');
    }

    public function index() {
        $sale_returns = SaleReturn::search()->latest()->paginate(15);
        return view('shopboss::salesreturn.index',compact('sale_returns'));
    }

    public function create() 
    {
        $customers = Customer::selectRaw('
                                    id,
                                    customer_name as text,
                                    customer_phone as subText
                                ')
                                ->get();
        return view('shopboss::salesreturn.create',compact('customers'));
    }

    public function store(Request $request) {
        try {
            $req = $request->all();

            $products = [];
            DB::beginTransaction();
            foreach ($req['products'] as $item) {
                $saleDetail = SaleDetails::with('sale')->findOrFail($item['product_id']);
                array_push($products, [
                    'branch_id'      => 1,
                    'product_id'     => $saleDetail->product_id,
                    'product_name'   => $saleDetail->product->product_name,
                    'product_code'   => $saleDetail->product->product_code,
                    'sale_id'        => $saleDetail->sale_id,
                    'sale_detail_id' => $saleDetail->id,
                    'unit_price'     => $saleDetail->unit_price,
                    'quantity'       => $item['qty'],
                    'sub_total'      => $item['qty'] * $saleDetail->unit_price,
                ]);
                $purchase_detail = PurchaseDetail::find($saleDetail->purchase_detail_id);

                $purchase_detail->update([
                    'sale_qty'      => $purchase_detail->sale_qty - $item['qty'],
                    'available_qty' => $purchase_detail->available_qty + $item['qty'],
                ]);
                
                $saleDetail->increment('return_qty',$item['qty']);
            }

            $customer = Customer::find($req['customer_id']);
            if(is_null($customer)) throw new Exception("Customer not found", 404);

            $totalSubTotal = collect($products)->sum('sub_total');
            $payload = [
                'date'               => $req['date'],
                'branch_id'          => 1,
                'sale_id'            => $saleDetail->sale_id,
                'customer_id'        => $customer->id,
                'customer_name'      => $customer->customer_name,
                'paid_amount'        => $req['paid_amount'],
                'payment_method'     => $req['payment_method'],
                'note'               => $req['note'],
            ];
            
            $payload['total_amount'] = $totalSubTotal;
            $payload['due_amount']   = $payload['total_amount'] - $payload['paid_amount'];

            if ($payload['due_amount'] == $payload['total_amount']) {
                $payment_status = 'Unpaid';
            } elseif ($payload['due_amount'] > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }
            $payload['payment_status'] = $payment_status;
            $saleReturn = SaleReturn::create($payload);

            if ($payload['paid_amount'] > 0) {
                SaleReturnPayment::create([
                    'date'           => $req['date'],
                    'reference'      => 'INV/' . $saleReturn->reference,
                    'amount'         => $saleReturn->paid_amount,
                    'sale_return_id' => $saleReturn->id,
                    'payment_method' => $req['payment_method']
                ]);
            }
            foreach ($products as $product) {
                SaleReturnDetail::create(array_merge([
                    'sale_return_id' => $saleReturn->id
                ], $product));
            }
            DB::commit();
            return redirect()->route('sale-returns.index')->withSuccess(__('Sale Return Successfull'));

        } catch (Exception $th) {
            DB::rollBack();
            return redirect()->route('sale-returns.index')->withSuccess(__($th->getMessage()));
        }
    }

    public function show($id) 
    {
        $sale_return = SaleReturn::find($id);
        $customer    = Customer::findOrFail($sale_return->customer_id);

        return view('shopboss::salesreturn.show', compact('sale_return', 'customer'));
    }

    public function edit($id) 
    {
        $sale_return = SaleReturn::find($id);
        $customers = Customer::selectRaw('
                                    id,
                                    customer_name as text,
                                    customer_phone as subText
                                ')
                                ->get();
        return view('shopboss::salesreturn.edit', compact('sale_return','customers'));
    }

    public function update(Request $request,$id) {
        try {
            $req = $request->all();
            $saleReturn = SaleReturn::find($id);

            DB::beginTransaction();
            foreach ($req['products'] as $item) {
                $saleReturnDetail = SaleReturnDetail::findOrFail($item['product_id']);
                $product              = Product::find($saleReturnDetail->product_id);
                $payload = [
                    'unit_price'     => $saleReturnDetail->unit_price,
                    'quantity'       => $item['qty'],
                    'sub_total'      => $item['qty'] * $saleReturnDetail->unit_price,
                ];
                $saleDetail = SaleDetails::find($saleReturnDetail->sale_detail_id);
                $purchase_detail = PurchaseDetail::find($saleDetail->purchase_detail_id);

                $purchase_detail->update([
                    'sale_qty'      => ($purchase_detail->sale_qty + $saleReturnDetail->quantity) - $item['qty'],
                    'available_qty' => ($purchase_detail->available_qty - $saleReturnDetail->quantity) + $item['qty'],
                ]);

                
                $saleDetail->update(['return_qty' => ($saleDetail->return_qty - $saleReturnDetail->quantity) + $item['qty'] ]);
                $saleReturnDetail->update($payload);
            }

            $totalSubTotal = $saleReturn->saleReturnDetails->sum('sub_total');
            $payload = [
                'date'               => $req['date'],
                'note'               => $req['note'],
            ];
            
            $payload['total_amount'] = $totalSubTotal;
            $payload['due_amount']   = $payload['total_amount'] - $saleReturn->paid_amount;

            if ($payload['due_amount'] == $payload['total_amount']) {
                $payment_status = 'Unpaid';
            } elseif ($payload['due_amount'] > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }
            $payload['payment_status'] = $payment_status;
            $saleReturn->update($payload);

            DB::commit();
            return redirect()->route('sale-returns.index')->withSuccess("Sale Return Updated");
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('sale-returns.index')->withErrors($e->getMessage());
        }
    }
    public function destroy($id) {
        $saleReturn = SaleReturn::with('saleReturnDetails', 'saleReturnPayments')->findOrFail($id);

        foreach ($saleReturn->saleReturnDetails as $saleReturnDetails) 
        {
            $saleReturnDetails->saleDetails->decrement('return_qty',$saleReturnDetails->quantity);
        }

        $saleReturn->saleReturnDetails()->delete();
        $saleReturn->saleReturnPayments()->delete();
        $saleReturn->delete();
        return redirect()->route('sale-returns.index')->withSuccess("Sale Return deleted");
    }
    
}
