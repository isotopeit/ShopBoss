<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Isotope\ShopBoss\Models\PurchaseReturn;
use Isotope\ShopBoss\Models\PurchaseReturnPayment;

class PurchaseReturnPaymentsController extends Controller
{
    public static $permissions = [
        'index'   => ['access_purchase_return_payments', 'Purchase Return Payments List'],
        'create'  => ['create_purchase_return_payments', 'Purchase Return Payments Create'],
        'store'   => ['store_purchase_return_payments', 'Purchase Return Payments Store'],
        'edit'    => ['edit_purchase_return_payments', 'Purchase Return Payments Edit'],
        'update'  => ['update_purchase_return_payments', 'Purchase Return Payments Update'],
        'destroy' => ['delete_purchase_return_payments', 'Purchase Return Payments Delete'],
    ];

    public function index(Request $request)
    {
        $purchaseReturn = PurchaseReturn::with('purchaseReturnPayments')->findOrFail($request->input('purchase_return_id'));
        return view('shopboss::purchases-return.payments.index', compact('purchaseReturn'));
    }

    public function create(Request $request)
    {
        $purchaseReturn = PurchaseReturn::with('purchaseReturnPayments')->findOrFail($request->input('purchase_return_id'));
        return view('shopboss::purchases-return.payments.create', compact('purchaseReturn'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'               => 'required|date',
            'reference'          => 'required|string|max:64',
            'amount'             => 'required|numeric',
            'note'               => 'nullable|string|max:255',
            'purchase_return_id' => 'required',
            'payment_method'     => 'required|string|max:32'
        ]);

        try {
            PurchaseReturnPayment::create([
                'date'               => $request->date,
                'reference'          => $request->reference,
                'amount'             => $request->amount,
                'note'               => $request->note,
                'purchase_return_id' => $request->purchase_return_id,
                'payment_method'     => $request->payment_method
            ]);

            $purchaseReturn = PurchaseReturn::findOrFail($request->purchase_return_id);
            if ($purchaseReturn->due_amount < $request->amount)
                throw new Exception("over amount not acceptable", 400);

            $due_amount = $purchaseReturn->due_amount - $request->amount;

            if ($due_amount == $purchaseReturn->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $purchaseReturn->update([
                'paid_amount'    => $purchaseReturn->paid_amount + $request->amount,
                'due_amount'     => $due_amount,
                'payment_status' => $payment_status
            ]);
            return redirect()->route('purchase-returns.index')->withSuccess('Purchase Return Payment Created!');
        } catch (Exception $e) {
            return redirect()->route('purchase-returns.index')->withErrors($e->getMessage());
        }
    }

    public function edit($id)
    {
        $payment = PurchaseReturnPayment::with('purchaseReturn')->findOrFail($id);
        return view('shopboss::purchases-return.payments.edit', compact('payment'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date'           => 'required|date',
            'reference'      => 'required|string|max:64',
            'amount'         => 'required|numeric',
            'note'           => 'nullable|string|max:255',
            'payment_method' => 'required|string|max:32'
        ]);
        try {
            DB::beginTransaction();
            $payment = PurchaseReturnPayment::with('purchaseReturn')->findOrFail($id);

            $due_amount = ($payment->purchaseReturn->due_amount + $payment->amount) - $request->amount;
            if ($due_amount == $payment->purchaseReturn->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $payment->purchaseReturn->update([
                'paid_amount'    => ($payment->purchaseReturn->paid_amount - $payment->amount) + $request->amount,
                'due_amount'     => $due_amount,
                'payment_status' => $payment_status
            ]);

            $payment->update([
                'date'           => $request->date,
                'amount'         => $request->amount,
                'note'           => $request->note,
                'payment_method' => $request->payment_method
            ]);

            DB::commit();
            return redirect()->route('purchase-returns.index', 'purchase_return_id=' . $payment->purchase_return_id)->withSuccess('Purchase Return Payment Updated!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('purchase-returns.index', 'purchase_return_id=' . $payment->purchase_return_id)->withErrors($e->getMessage());
        }
    }

    public function destroy($id)
    {
        $payment = PurchaseReturnPayment::with('purchaseReturn')->findOrFail($id);;
        $id = $payment->purchase_return_id;

        $payment->purchaseReturn->update([
            'paid_amount'    => $payment->purchaseReturn->paid_amount - $payment->amount,
            'due_amount'     => $payment->purchaseReturn->due_amount + $payment->amount,
        ]);

        $payment->delete();

        return redirect()->route('purchase-returns.index', 'purchase_return_id=' . $id)->withSuccess('Purchase Return Payment Deleted!');
    }
}
