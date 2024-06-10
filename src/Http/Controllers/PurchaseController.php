<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\Purchase;
use Isotope\ShopBoss\Models\Supplier;
use Isotope\ShopBoss\Models\PurchaseDetail;
use Isotope\ShopBoss\Models\PurchasePayment;

class PurchaseController extends Controller
{
    public static $permissions = [
        'index'   => ['access_purchases', 'Purchase List'],
        'create'  => ['create_purchases', 'Purchase Create'],
        'store'   => ['store_purchases', 'Purchase Store'],
        'show'    => ['show_purchases', 'Purchase Show'],
        'edit'    => ['edit_purchases', 'Purchase Edit'],
        'update'  => ['update_purchases', 'Purchase Update'],
        'destroy' => ['delete_purchases', 'Purchase Delete'],
    ];

    public function index()
    {
        $purchases = Purchase::search()->orderBydesc('id')->paginate(15);
        return view('shopboss::purchase.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::selectRaw("
                            id,
                            supplier_name as text,
                            supplier_phone as subText
                        ")->get();
        return view('shopboss::purchase.create', compact('suppliers'));
    }


    public function store(Request $request)
    {
        try {
            $req = $request->all();
            DB::beginTransaction();

            $products = [];

            foreach ($req['products'] as $item) {
                $product  = Product::find($item['product_id']);
                $discount = array_key_exists('percentage', $item) ? ($product->product_cost / 100) * floatval($item['discount']) : $item['discount'];
                array_push($products, [
                    'branch_id'               => 1,
                    'product_id'              => $product->id,
                    'product_name'            => $product->product_name,
                    'product_code'            => $product->product_code,
                    'purchase_qty'            => $item['qty'],
                    'available_qty'           => $item['qty'],
                    'unit_price'              => $product->product_cost,
                    'sub_total'               => ($product->product_cost - $discount) * floatval($item['qty']),
                    'product_discount_amount' => $discount,
                    'product_tax_amount'      => 0,
                ]);
            }

            $supplier = Supplier::find($req['supplier_id']);
            if(is_null($supplier)) throw new Exception("Supplier not found", 404);

            $totalSubTotal = collect($products)->sum('sub_total');
            $payload = [
                'branch_id'           => 1,
                'supplier_id'         => $supplier->id,
                'supplier_name'       => $supplier->supplier_name,
                'date'                => $req['date'],
                'tax_percentage'      => $req['tax_percentage'],
                'tax_amount'          => ($totalSubTotal / 100) * $req['tax_percentage'],
                'discount_percentage' => (100/$totalSubTotal)*$req['discount_amount'],
                'discount_amount'     => $req['discount_amount'],
                'note'                => $req['note'],
                'shipping_amount'     => $req['shipping_amount'],
                'paid_amount'         => $req['paid_amount'],
                'payment_method'      => $req['payment_method'],
            ];

            $payload['total_amount'] = $totalSubTotal + $req['shipping_amount'] + $payload['tax_amount'] - $req['discount_amount'];
            $payload['due_amount']   = $payload['total_amount'] - $payload['paid_amount'];

            if ($payload['due_amount'] == $payload['total_amount']) {
                $payment_status = 'Unpaid';
            } elseif ($payload['due_amount'] > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }
            $payload['payment_status'] = $payment_status;
            $purchase = Purchase::create($payload);

            if ($payload['paid_amount'] > 0) {
                PurchasePayment::create([
                    'date'           => $req['date'],
                    'reference'      => 'INV/' . $purchase->reference,
                    'amount'         => $purchase->paid_amount,
                    'purchase_id'    => $purchase->id,
                    'payment_method' => $req['payment_method']
                ]);
            }
            foreach ($products as $product) {
                PurchaseDetail::create(array_merge([
                    'purchase_id' => $purchase->id
                ], $product));
            }

            DB::commit();
            return redirect()->route('purchases.index')->withSuccess("Purchase Created");
        } catch (Exception $e) {
            dd($e);
            return redirect()->route('purchases.index')->withErrors($e->getMessage());
        }
    }


    public function show($id)
    {
        $purchase = Purchase::with('supplier', 'purchaseDetails')->findOrFail($id);
        return view('shopboss::purchase.show', compact('purchase'));
    }


    public function edit($id)
    {
        $suppliers = Supplier::selectRaw("
                            id,
                            supplier_name as text,
                            supplier_phone as subText
                        ")->get();
        $purchase = Purchase::with('purchaseDetails.product')->find($id);
        return view('shopboss::purchase.edit', compact('purchase', 'suppliers'));
    }


    public function update(Request $request, $id)
    {
        try
        {
            $req = $request->all();
            $purchase = Purchase::with('purchaseDetails.product')->find($id);

            DB::beginTransaction();

            foreach ($req['products'] as $item) {
                $product  = Product::find($item['product_id']);
                $discount = array_key_exists('percentage', $item) ? ($product->product_cost / 100) * floatval($item['discount']) : $item['discount'];
                $payload  = [
                    'product_id'              => $product->id,
                    'product_name'            => $product->product_name,
                    'product_code'            => $product->product_code,
                    'purchase_qty'            => $item['qty'],
                    'available_qty'           => $item['qty'],
                    'unit_price'              => $product->product_cost,
                    'sub_total'               => ($product->product_cost - $discount) * floatval($item['qty']),
                    'product_discount_amount' => $discount,
                    'product_tax_amount'      => 0,
                ];

                if (array_key_exists('detail_id', $item)) {
                    $detail = PurchaseDetail::find($item['detail_id']);
                    $detail->update($payload);
                } else {
                    $purchase->purchaseDetails()->create($payload);
                }
            }

            $supplier = Supplier::findOrFail($request->supplier_id);
            if(is_null($supplier)) throw new Exception("Supplier not found", 404);
            $purchase->refresh();

            $totalSubTotal = $purchase->purchaseDetails->sum('sub_total');
            $payload = [
                'supplier_id'         => $supplier->id,
                'supplier_name'       => $supplier->supplier_name,
                'date'                => $req['date'],
                'tax_percentage'      => $req['tax_percentage'],
                'tax_amount'          => ($totalSubTotal / 100) * $req['tax_percentage'],
                'discount_percentage' => (100/$totalSubTotal)*$req['discount_amount'],
                'discount_amount'     => $req['discount_amount'],
                'note'                => $req['note'],
                'shipping_amount'     => $req['shipping_amount'],
                'total_amount'        => $totalSubTotal + $req['shipping_amount'] + $purchase->tax_amount - $req['discount_amount'],
            ];

            $payload['due_amount'] = $payload['total_amount'] - $purchase->paid_amount;
            if ($payload['due_amount'] == $payload['total_amount']) {
                $payment_status = 'Unpaid';
            } elseif ($payload['due_amount'] > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }
            $payload['payment_status'] = $payment_status;

            $purchase->update($payload);

            DB::commit();
            return redirect()->route('purchases.index')->withSuccess("Purchase Updated");
        }
        catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('purchases.index')->withErrors($e->getMessage());
        }
    }


    public function destroy($id)
    {
        $purchase = Purchase::with('purchaseDetails', 'purchasePayments', 'purchaseReturns')->findOrFail($id);
        $purchase->purchaseDetails()->delete();
        $purchase->purchasePayments()->delete();
        $purchase->delete();
        return redirect()->route('purchases.index')->withSuccess("Purchase deleted");
    }

    public function pdf($id)
    {
        $purchase = Purchase::findOrFail($id);
        $supplier = Supplier::findOrFail($purchase->supplier_id);

        $pdf = \PDF::loadView('shopboss::purchase.print', [
            'purchase' => $purchase,
            'supplier' => $supplier,
        ])->setPaper('a4');

        return $pdf->stream('purchase-' . $purchase->reference . '.pdf');
    }
}
