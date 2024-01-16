<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\Customer;
use Isotope\ShopBoss\Models\Quotation;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Isotope\ShopBoss\Models\QuotationDetails;
use Gloudemans\Shoppingcart\Facades\Cart;
use Isotope\ShopBoss\Http\Requests\StoreQuotationRequest;
use Isotope\ShopBoss\Http\Requests\UpdateQuotationRequest;
use Isotope\ShopBoss\Http\Services\DataTables\QuotationsDataTable;

class QuotationController extends Controller
{
    public static $permissions = [
        'index'   => ['access_quotations', 'Quotation List'],
        'create'  => ['create_quotations', 'Quotation Create'],
        'store'   => ['store_quotations', 'Quotation Store'],
        'show'    => ['show_quotations', 'Quotation Show'],
        'edit'    => ['edit_quotations', 'Quotation Edit'],
        'update'  => ['update_quotations', 'Quotation Update'],
        'destroy' => ['delete_quotations', 'Quotation Delete'],
    ];

    public function index(QuotationsDataTable $dataTable) {
        abort_if(Gate::denies('access_quotations'), 403);

        return $dataTable->render('pos::quotation.index');
    }


    public function create() {
        abort_if(Gate::denies('create_quotations'), 403);

        Cart::instance('quotation')->destroy();
        $customers = Customer::all();

        return view('pos::quotation.create', compact('customers'));
    }


    public function store(StoreQuotationRequest $request) {
        DB::transaction(function () use ($request) {
            $tax      = str_replace(',','',Cart::instance('quotation')->tax());
            $discount = str_replace(',','',Cart::instance('quotation')->discount());
            
            $quotation = Quotation::create([
                'date'                => $request->date,
                'customer_id'         => $request->customer_id,
                'customer_name'       => Customer::findOrFail($request->customer_id)->customer_name,
                'tax_percentage'      => $request->tax_percentage,
                'discount_percentage' => $request->discount_percentage,
                'shipping_amount'     => $request->shipping_amount * 100,
                'total_amount'        => $request->total_amount * 100,
                'status'              => $request->status,
                'note'                => $request->note,
                'tax_amount'          => (float)$tax * 100,
                'discount_amount'     => (float)$discount * 100,
            ]);

            foreach (Cart::instance('quotation')->content() as $cart_item) {
                QuotationDetails::create([
                    'quotation_id'            => $quotation->id,
                    'product_id'              => $cart_item->id,
                    'product_name'            => $cart_item->name,
                    'product_code'            => $cart_item->options->code,
                    'quantity'                => $cart_item->qty,
                    'price'                   => $cart_item->price * 100,
                    'unit_price'              => $cart_item->options->unit_price * 100,
                    'sub_total'               => $cart_item->options->sub_total * 100,
                    'product_discount_amount' => $cart_item->options->product_discount * 100,
                    'product_discount_type'   => $cart_item->options->product_discount_type,
                    'product_tax_amount'      => $cart_item->options->product_tax * 100,
                ]);
            }

            Cart::instance('quotation')->destroy();
        });

        toast('Quotation Created!', 'success');

        return redirect()->route('quotations.index');
    }


    public function show(Quotation $quotation) {
        abort_if(Gate::denies('show_quotations'), 403);

        $customer = Customer::findOrFail($quotation->customer_id);

        return view('pos::quotation.show', compact('quotation', 'customer'));
    }


    public function edit(Quotation $quotation) {
        abort_if(Gate::denies('edit_quotations'), 403);

        $quotation_details = $quotation->quotationDetails;

        Cart::instance('quotation')->destroy();

        $cart = Cart::instance('quotation');

        foreach ($quotation_details as $quotation_detail) {
            $cart->add([
                'id'      => $quotation_detail->product_id,
                'name'    => $quotation_detail->product_name,
                'qty'     => $quotation_detail->quantity,
                'price'   => $quotation_detail->price,
                'weight'  => 1,
                'options' => [
                    'product_discount' => $quotation_detail->product_discount_amount,
                    'product_discount_type' => $quotation_detail->product_discount_type,
                    'sub_total'   => $quotation_detail->sub_total,
                    'code'        => $quotation_detail->product_code,
                    'stock'       => Product::findOrFail($quotation_detail->product_id)->product_quantity,
                    'product_tax' => $quotation_detail->product_tax_amount,
                    'unit_price'  => $quotation_detail->unit_price
                ]
            ]);
        }

        return view('pos::quotation.edit', compact('quotation'));
    }


    public function update(UpdateQuotationRequest $request, Quotation $quotation) {
        DB::transaction(function () use ($request, $quotation) {
            foreach ($quotation->quotationDetails as $quotation_detail) {
                $quotation_detail->delete();
            }

            $tax      = str_replace(',','',Cart::instance('quotation')->tax());
            $discount = str_replace(',','',Cart::instance('quotation')->discount());

            $quotation->update([
                'date'                => $request->date,
                'reference'           => $request->reference,
                'customer_id'         => $request->customer_id,
                'customer_name'       => Customer::findOrFail($request->customer_id)->customer_name,
                'tax_percentage'      => $request->tax_percentage,
                'discount_percentage' => $request->discount_percentage,
                'shipping_amount'     => $request->shipping_amount * 100,
                'total_amount'        => $request->total_amount * 100,
                'status'              => $request->status,
                'note'                => $request->note,
                'tax_amount'          => (float)$tax * 100,
                'discount_amount'     => (float)$discount * 100,
            ]);

            foreach (Cart::instance('quotation')->content() as $cart_item) {
                QuotationDetails::create([
                    'quotation_id'            => $quotation->id,
                    'product_id'              => $cart_item->id,
                    'product_name'            => $cart_item->name,
                    'product_code'            => $cart_item->options->code,
                    'quantity'                => $cart_item->qty,
                    'price'                   => $cart_item->price * 100,
                    'unit_price'              => $cart_item->options->unit_price * 100,
                    'sub_total'               => $cart_item->options->sub_total * 100,
                    'product_discount_amount' => $cart_item->options->product_discount * 100,
                    'product_discount_type'   => $cart_item->options->product_discount_type,
                    'product_tax_amount'      => $cart_item->options->product_tax * 100,
                ]);
            }

            Cart::instance('quotation')->destroy();
        });

        toast('Quotation Updated!', 'info');

        return redirect()->route('quotations.index');
    }


    public function destroy(Quotation $quotation) {
        abort_if(Gate::denies('delete_quotations'), 403);

        $quotation->delete();

        toast('Quotation Deleted!', 'warning');

        return redirect()->route('quotations.index');
    }
}
