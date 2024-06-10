<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Isotope\ShopBoss\Models\Purchase;
use Isotope\ShopBoss\Models\PurchasePayment;
use Isotope\ShopBoss\Observers\PurchasePaymentObserver;

class PurchasePaymentsController extends Controller
{
    public static $permissions = [
        'index'   => ['access_purchase_payments', 'Purchase Payment List'],
        'create'  => ['create_purchase_payments', 'Purchase Payment Create'],
        'store'   => ['store_purchase_payments', 'Purchase Payment Store'],
        'edit'    => ['edit_purchase_payments', 'Purchase Payment Edit'],
        'update'  => ['update_purchase_payments', 'Purchase Payment Update'],
        'destroy' => ['delete_purchase_payments', 'Purchase Payment Delete'],
    ];

    public function index(Request $request) {
        $purchase = Purchase::with('purchasePayments')->findOrFail($request->input('purchase_id'));
        return view('shopboss::purchase.payments.index', compact('purchase'));
    }


    public function create(Request $request) {
        $purchase = Purchase::with('purchasePayments')->findOrFail($request->input('purchase_id'));
        return view('shopboss::purchase.payments.create', compact('purchase'));
    }


    public function store(Request $request) {
        $request->validate([
            'date'           => 'required|date',
            'reference'      => 'required|string|max:255',
            'amount'         => 'required|numeric',
            'note'           => 'nullable|string|max:1000',
            'purchase_id'    => 'required',
            'payment_method' => 'required|string|max:255'
        ]);

        try
        {
            DB::beginTransaction();
            $payment = PurchasePayment::create([
                'date'           => $request->date,
                'reference'      => $request->reference,
                'amount'         => $request->amount,
                'note'           => $request->note,
                'purchase_id'    => $request->purchase_id,
                'payment_method' => $request->payment_method
            ]);
            (new PurchasePaymentObserver())->created($payment);

            $purchase = Purchase::findOrFail($request->purchase_id);
            if($purchase->due_amount < $request->amount)
                throw new Exception("Over amount not acceptable", 400);

            $due_amount = $purchase->due_amount - $request->amount;

            if ($due_amount == $purchase->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }
            $purchase->update([
                'paid_amount'    => $purchase->paid_amount + $request->amount,
                'due_amount'     => $due_amount,
                'payment_status' => $payment_status
            ]);
            DB::commit();
            return redirect()->route('purchase-payments.index', 'purchase_id='.$request->purchase_id)->withSuccess('Purchase Payment Created!');
        }
        catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('purchase-payments.index', 'purchase_id='.$request->purchase_id)->withErrors($e->getMessage());
        }
    }


    public function edit($id) {
        $payment = PurchasePayment::with('purchase')->findOrFail($id);
        return view('shopboss::purchase.payments.edit', compact('payment'));
    }


    public function update(Request $request, $id) {
        $request->validate([
            'date'           => 'required|date',
            'reference'      => 'required|string|max:255',
            'amount'         => 'required|numeric',
            'note'           => 'nullable|string|max:1000',
            'payment_method' => 'required|string|max:255'
        ]);
        try
        {
            DB::beginTransaction();
            $payment = PurchasePayment::with('purchase')->findOrFail($id);

            $due_amount = ($payment->purchase->due_amount + $payment->amount) - $request->amount;
            if ($due_amount == $payment->purchase->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $payment->purchase->update([
                'paid_amount'    => ($payment->purchase->paid_amount - $payment->amount) + $request->amount,
                'due_amount'     => $due_amount,
                'payment_status' => $payment_status
            ]);

            $payment->update([
                'date'           => $request->date,
                'amount'         => $request->amount,
                'note'           => $request->note,
                'payment_method' => $request->payment_method
            ]);
            (new PurchasePaymentObserver())->updated($payment);
            DB::commit();
            return redirect()->route('purchase-payments.index', 'purchase_id='.$payment->purchase_id)->withSuccess('Purchase Payment Updated!');
        }
        catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('purchase-payments.index', 'purchase_id='.$payment->purchase_id)->withErrors($e->getMessage());
        }
    }


    public function destroy($id) {
        $payment = PurchasePayment::with('purchase')->findOrFail($id);        ;
        $id = $payment->purchase_id;

        $payment->purchase->update([
            'paid_amount'    => $payment->purchase->paid_amount - $payment->amount,
            'due_amount'     => $payment->purchase->due_amount + $payment->amount,
        ]);
        (new PurchasePaymentObserver())->deleted($payment);
        $payment->delete();

        return redirect()->route('purchase-payments.index', 'purchase_id='.$id)->withSuccess('Purchase Payment Deleted!');
    }
}
