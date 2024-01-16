<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Sale;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Isotope\ShopBoss\Models\SaleReturn;
use Isotope\ShopBoss\Models\SaleReturnPayment;
use Isotope\ShopBoss\Http\Services\DataTables\SaleReturnPaymentsDataTable;

class SaleReturnPaymentsController extends Controller
{
    public static $permissions = [
        'index'   => ['access_sale_return_payments', 'Sale Return Payments List'],
        'create'  => ['create_sale_return_payments', 'Sale Return Payments Create'],
        'store'   => ['store_sale_return_payments', 'Sale Return Payments Store'],
        'edit'    => ['edit_sale_return_payments', 'Sale Return Payments Edit'],
        'update'  => ['update_sale_return_payments', 'Sale Return Payments Update'],
        'destroy' => ['delete_sale_return_payments', 'Sale Return Payments Delete'],
    ];

    public function index($sale_return_id) {
        $sale_return = SaleReturn::findOrFail($sale_return_id);
        return view('shopboss::salesreturn.payments.index', compact('sale_return'));
    }

    public function create($sale_return_id) {
        $sale_return = SaleReturn::findOrFail($sale_return_id);
        return view('shopboss::salesreturn.payments.create', compact('sale_return'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'               => 'required|date',
            'reference'          => 'required|string|max:64',
            'amount'             => 'required|numeric',
            'note'               => 'nullable|string|max:255',
            'sale_return_id'     => 'required',
            'payment_method'     => 'required|string|max:32'
        ]);

        try {
            SaleReturnPayment::create([
                'date'               => $request->date,
                'reference'          => $request->reference,
                'amount'             => $request->amount,
                'note'               => $request->note,
                'sale_return_id'     => $request->sale_return_id,
                'payment_method'     => $request->payment_method
            ]);

            $saleReturn = SaleReturn::findOrFail($request->sale_return_id);
            if ($saleReturn->due_amount < $request->amount)
                throw new Exception("over amount not acceptable", 400);

            $due_amount = $saleReturn->due_amount - $request->amount;

            if ($due_amount == $saleReturn->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $saleReturn->update([
                'paid_amount'    => $saleReturn->paid_amount + $request->amount,
                'due_amount'     => $due_amount,
                'payment_status' => $payment_status
            ]);
            return redirect()->route('sale-return-payments.index',$saleReturn->id)->withSuccess('Sale Return Payment Created!');
        } catch (Exception $e) {
            return redirect()->route('sale-returns.index')->withErrors($e->getMessage());
        }
    }

    public function edit($sale_return_payment_id) 
    {
        $payment = SaleReturnPayment::findOrFail($sale_return_payment_id);

        return view('shopboss::salesreturn.payments.edit', compact('payment'));
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
            $payment = SaleReturnPayment::findOrFail($id);

            $due_amount = ($payment->saleReturn->due_amount + $payment->amount) - $request->amount;
            if ($due_amount == $payment->saleReturn->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $payment->saleReturn->update([
                'paid_amount'    => ($payment->saleReturn->paid_amount - $payment->amount) + $request->amount,
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
            return redirect()->route('sale-return-payments.index', $payment->sale_return_id)->withSuccess('Sale Return Payment Updated!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('sale-return.index')->withErrors($e->getMessage());
        }
    }

    public function destroy($id)
    {
        $payment = SaleReturnPayment::findOrFail($id);;
        $id = $payment->sale_return_id;

        $payment->saleReturn->update([
            'paid_amount'    => $payment->saleReturn->paid_amount - $payment->amount,
            'due_amount'     => $payment->saleReturn->due_amount + $payment->amount,
        ]);

        $payment->delete();

        return redirect()->route('sale-returns.index',$id)->withSuccess('Sale Return Payment Deleted!');
    }
}
