<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Sale;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Isotope\ShopBoss\Models\SalePayment;
use Isotope\ShopBoss\Observers\SalePaymentObserver;

class SalePaymentsController extends Controller
{
    public static $permissions = [
        'index'   => ['access_sale_payments', 'Sale Payment List'],
        'create'  => ['create_sale_payments', 'Sale Payment Create'],
        'store'   => ['store_sale_payments', 'Sale Payment Store'],
        'edit'    => ['edit_sale_payments', 'Sale Payment Edit'],
        'update'  => ['update_sale_payments', 'Sale Payment Update'],
        'destroy' => ['delete_sale_payments', 'Sale Payment Delete'],
    ];

    public function index()
    {
        $sale = Sale::findOrFail(request()->sale_id);
        return view('shopboss::sale.payments.index', compact('sale'));
    }


    public function create() {

        $sale = Sale::findOrFail(request()->sale_id);

        return view('shopboss::sale.payments.create', compact('sale'));
    }


    public function store(Request $request) {
        $request->validate([
            'date'           => 'required|date',
            'reference'      => 'required|string|max:255',
            'amount'         => 'required|numeric',
            'note'           => 'nullable|string|max:1000',
            'sale_id'        => 'required',
            'payment_method' => 'required|string|max:255'
        ]);

        try {
            $payment = SalePayment::create([
                'date'           => $request->date,
                'reference'      => $request->reference,
                'amount'         => $request->amount,
                'note'           => $request->note,
                'sale_id'        => $request->sale_id,
                'payment_method' => $request->payment_method
            ]);
            (new SalePaymentObserver())->created($payment);
            $sale = Sale::findOrFail($request->sale_id);

            $due_amount = $sale->due_amount - $request->amount;

            if ($due_amount == $sale->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $sale->update([
                'paid_amount'    => ($sale->paid_amount + $request->amount),
                'due_amount'     => $due_amount,
                'payment_status' => $payment_status
            ]);
            DB::commit();
            return redirect()->route('sale-payments.index', 'sale_id='.$request->sale_id)->withSuccess('Sale Payment Created!');
        }
        catch (Exception $e) {
            DB::commit();
            return redirect()->route('sale-payments.index', 'sale_id='.$request->sale_id)->withErrors($e->getMessage());
        }
    }


    public function edit($uuid) {
        $payment = SalePayment::firstWhere('uuid',$uuid);
        return view('shopboss::sale.payments.edit', compact('payment'));
    }


    public function update(Request $request, $payment_id)
    {
        $request->validate([
            'date'           => 'required|date',
            'reference'      => 'required|string|max:255',
            'amount'         => 'required|numeric',
            'note'           => 'nullable|string|max:1000',
            'payment_method' => 'required|string|max:255'
        ]);

        try {
            $salePayment = SalePayment::find($payment_id);
            $sale = $salePayment->sale;

            $due_amount = ($sale->due_amount + $salePayment->amount) - $request->amount;

            if ($due_amount == $sale->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }
            DB::beginTransaction();
            $sale->update([
                'paid_amount' => (($sale->paid_amount - $salePayment->amount) + $request->amount),
                'due_amount' => $due_amount,
                'payment_status' => $payment_status
            ]);

            $salePayment->update([
                'date'           => $request->date,
                'reference'      => $request->reference,
                'amount'         => $request->amount,
                'note'           => $request->note,
                'payment_method' => $request->payment_method
            ]);
            (new SalePaymentObserver())->updated($salePayment);
            DB::commit();
            return redirect()->route('sale-payments.index', 'sale_id='.$salePayment->sale_id)->withSuccess('Sale Payment Updated!');
        }
        catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('sale-payments.index', 'sale_id='.$salePayment->sale_id)->withErrors($e->getMessage());
        }

    }


    public function destroy($uuid)
    {
        try{
            DB::beginTransaction();
            $payment = SalePayment::firstWhere('uuid',$uuid);
            $id = $payment->sale->id;

            $payment->sale->update([
                'paid_amount'    => $payment->sale->paid_amount - $payment->amount,
                'due_amount'     => $payment->sale->due_amount + $payment->amount,
            ]);
            (new SalePaymentObserver())->deleted($payment);
            $payment->delete();
            DB::commit();
            return redirect()->route('sale-payments.index', 'sale_id='.$id)->withSuccess('Sale Payment Deleted!');
        }
        catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('sale-payments.index', 'sale_id='.$salePayment->sale_id)->withErrors($e->getMessage());
        }
    }
}
