<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Isotope\ShopBoss\Models\Supplier;
use Isotope\ShopBoss\Models\PurchaseReturn;
use Isotope\ShopBoss\Models\PurchaseReturnDetail;
use Isotope\ShopBoss\Models\PurchaseReturnPayment;
use Isotope\ShopBoss\Models\PurchaseDetail;

class PurchasesReturnController extends Controller
{
    public static $permissions = [
        'index'   => ['access_purchase_returns', 'Purchase Returns List'],
        'create'  => ['create_purchase_returns', 'Purchase Returns Create'],
        'store'   => ['store_purchase_returns', 'Purchase Returns Store'],
        'show'    => ['show_purchase_returns', 'Purchase Returns Show'],
        'edit'    => ['edit_purchase_returns', 'Purchase Returns Edit'],
        'update'  => ['update_purchase_returns', 'Purchase Returns Update'],
        'destroy' => ['delete_purchase_returns', 'Purchase Returns Delete'],
    ];
    
    public function pdf($id)
    {
        $purchaseReturn = PurchaseReturn::findOrFail($id);
        $supplier = Supplier::findOrFail($purchaseReturn->supplier_id);

        $pdf = \PDF::loadView('shopboss::purchases-return.print', [
            'purchase_return' => $purchaseReturn,
            'supplier' => $supplier,
        ])->setPaper('a4');

        return $pdf->stream('purchase-return-'. $purchaseReturn->reference .'.pdf');
    }

    public function index() {
        $returns = PurchaseReturn::search()->orderByDesc('id')->paginate(12);
        return view('shopboss::purchases-return.index', compact('returns'));
    }


    public function create() {
        $suppliers = Supplier::selectRaw("
                            id,
                            supplier_name as text,
                            supplier_phone as subText
                        ")->get();
        return view('shopboss::purchases-return.create',compact('suppliers'));
    }


    public function store(Request $request) {
        try {
            $req = $request->all();
            DB::beginTransaction();

            $products = [];

            foreach ($req['products'] as $item) {
                $purchaseDetail = PurchaseDetail::with('purchase')->findOrFail($item['product_id']);
                array_push($products, [
                    'branch_id'          => 1,
                    'product_id'         => $purchaseDetail->product_id,
                    'purchase_id'        => $purchaseDetail->purchase_id,
                    'purchase_detail_id' => $purchaseDetail->id,
                    'unit_price'         => $purchaseDetail->unit_price,
                    'quantity'           => $item['qty'],
                    'sub_total'          => $item['qty'] * $purchaseDetail->unit_price,
                ]);
                $purchaseDetail->decrement('available_qty', $item['qty']);
                $purchaseDetail->increment('purchase_return_qty', $item['qty']);
            }

            $supplier = Supplier::find($req['supplier_id']);
            if(is_null($supplier)) throw new Exception("Supplier not found", 404);

            $totalSubTotal = collect($products)->sum('sub_total');
            $payload = [
                'date'               => $req['date'],
                'branch_id'          => 1,
                'purchase_id'        => $purchaseDetail->purchase_id,
                'supplier_id'        => $supplier->id,
                'supplier_name'      => $supplier->supplier_name,
                'damaged_percentage' => (100/$totalSubTotal)*$req['damaged_price'],
                'damaged_amount'     => $req['damaged_price'],
                'paid_amount'        => $req['paid_amount'],
                'payment_method'     => $req['payment_method'],
                'note'               => $req['note'],
            ];
            
            $payload['total_amount'] = $totalSubTotal - $req['damaged_price'];
            $payload['due_amount']   = $payload['total_amount'] - $payload['paid_amount'];

            if ($payload['due_amount'] == $payload['total_amount']) {
                $payment_status = 'Unpaid';
            } elseif ($payload['due_amount'] > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }
            $payload['payment_status'] = $payment_status;
            $purchaseReturn = PurchaseReturn::create($payload);

            if ($payload['paid_amount'] > 0) {
                PurchaseReturnPayment::create([
                    'date'               => $req['date'],
                    'reference'          => 'INV/' . $purchaseReturn->reference,
                    'amount'             => $purchaseReturn->paid_amount,
                    'purchase_return_id' => $purchaseReturn->id,
                    'payment_method'     => $req['payment_method']
                ]);
            }
            foreach ($products as $product) {
                PurchaseReturnDetail::create(array_merge([
                    'purchase_return_id' => $purchaseReturn->id
                ], $product));
            }

            DB::commit();
            return redirect()->route('purchase-returns.index')->withSuccess("Purchase Return Created");
        } catch (Exception $e) {
            return redirect()->route('purchase-returns.index')->withErrors($e->getMessage());
        }
    }

    public function show($id) {
        $purchaseReturn = PurchaseReturn::with('supplier', 'purchaseReturnDetails.purchaseDetails')->findOrFail($id);
        return view('shopboss::purchases-return.show', compact('purchaseReturn'));
    }

    public function edit($id) {
        $purchaseReturn = PurchaseReturn::with('purchaseReturnDetails.purchaseDetails', 'purchase:reference,id')->findOrFail($id);
        $suppliers = Supplier::selectRaw("
                id,
                supplier_name as text,
                supplier_phone as subText
            ")->get();
        return view('shopboss::purchases-return.edit',compact('purchaseReturn', 'suppliers'));
    }

    public function update(Request $request, $id) {
        try {
            $req = $request->all();
            $purchaseReturn = PurchaseReturn::find($id);

            DB::beginTransaction();
            foreach ($req['products'] as $item) {
                $purchaseReturnDetail = PurchaseReturnDetail::with('purchaseDetails')->findOrFail($item['product_id']);
                $payload = [
                    'unit_price'         => $purchaseReturnDetail->unit_price,
                    'quantity'           => $item['qty'],
                    'sub_total'          => $item['qty'] * $purchaseReturnDetail->unit_price,
                ];
                $purchaseReturnDetail->purchaseDetails->update(['available_qty'=> ($purchaseReturnDetail->purchaseDetails->available_qty + $purchaseReturnDetail->quantity) - $item['qty']]);
                $purchaseReturnDetail->purchaseDetails->update(['purchase_return_qty'=> ($purchaseReturnDetail->purchaseDetails->purchase_return_qty - $purchaseReturnDetail->quantity) + $item['qty']]);

                $purchaseReturnDetail->update($payload);
            }

            $totalSubTotal = $purchaseReturn->purchaseReturnDetails->sum('sub_total');
            $payload = [
                'date'               => $req['date'],
                'damaged_percentage' => (100/$totalSubTotal)*$req['damaged_price'],
                'damaged_amount'     => $req['damaged_price'],
                'note'               => $req['note'],
            ];
            
            $payload['total_amount'] = $totalSubTotal - $req['damaged_price'];
            $payload['due_amount']   = $payload['total_amount'] - $purchaseReturn->paid_amount;

            if ($payload['due_amount'] == $payload['total_amount']) {
                $payment_status = 'Unpaid';
            } elseif ($payload['due_amount'] > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }
            $payload['payment_status'] = $payment_status;
            $purchaseReturn->update($payload);

            DB::commit();
            return redirect()->route('purchase-returns.index')->withSuccess("Purchase Return Updated");
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('purchase-returns.index')->withErrors($e->getMessage());
        }
    }

    public function destroy($id) {
        $purchaseReturn = PurchaseReturn::with('purchaseReturnDetails', 'purchaseReturnPayments')->findOrFail($id);
        $purchaseReturn->purchaseReturnPayments()->delete();
        $purchaseReturn->purchaseReturnDetails()->delete();
        $purchaseReturn->delete();
        return redirect()->route('purchase-returns.index')->withSuccess("Purchase Return deleted");
    }
}
